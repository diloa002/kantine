<?php

include "db.con.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $navn = $_POST['navn'];
    $email = $_POST['email'];
    $kostnadsted = $_POST['kostnadsted_belastes'];
    $bestillingsdato = $_POST['bestillingsdato'];
    $kommentar = '';
    $dato = date("Y-m-d, H:i:s"); // Rettet opp time formatet

    if (isset($_POST['kommentar']) && !empty($_POST['kommentar'])) {
        $kommentar = $_POST['kommentar'];
    }

    // Sett inn bestilling i databasen
    $sql_bestilling = "INSERT INTO bestilling (email, dato_bestilt, dato_levering, avdeling, kommentar) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_bestilling);

    if ($stmt) {
        // Binder parametere til spørringen og utfører den
        $stmt->bind_param("sssss", $email, $dato, $bestillingsdato, $kostnadsted, $kommentar);
        $stmt->execute();

        // Sjekk om spørringen ble utført vellykket
        if ($stmt->affected_rows > 0) {
            // Spørringen var vellykket, hent siste innsetting ID
            $last_inserted_id = $conn->insert_id;
            $stmt->close();

            $totalpris = 0;
            $bestilte_varer = array();

            // Loop gjennom menyelementene og sett inn bestilte varer
            foreach ($_POST['meny'] as $item => $value) {
                if (is_numeric($value) && $value > 0) {
                    $antall = $value;

                    // Hent meny detaljer
                    $sql_menu_item = "SELECT navn, pris FROM meny WHERE idmeny = ?";
                    $stmt_menu_item = $conn->prepare($sql_menu_item);
                    $stmt_menu_item->bind_param("i", $item);
                    $stmt_menu_item->execute();
                    $result_menu_item = $stmt_menu_item->get_result();
                    $menu_item = $result_menu_item->fetch_assoc();

                    $bestilte_varer[] = array(
                        'navn' => $menu_item['navn'],
                        'pris' => $menu_item['pris'],
                        'antall' => $antall
                    );

                    $stmt_menu_item->close();

                    $sql_insert_bestilling_meny = "INSERT INTO bestilling_has_meny (bestilling_idbestilling, meny_idmeny, antall) VALUES (?, ?, ?)";
                    $stmt_meny = $conn->prepare($sql_insert_bestilling_meny);
                    if ($stmt_meny) {
                        $stmt_meny->bind_param("iii", $last_inserted_id, $item, $antall);
                        $stmt_meny->execute();
                        $stmt_meny->close();
                    } else {
                        echo "Feil: Kunne ikke forberede spørringen for menyen.";
                    }
                }
            }

            // Beregn totalpris
            foreach ($bestilte_varer as $vare) {
                $total_pris_vare = $vare['pris'] * $vare['antall'];
                $totalpris += $total_pris_vare;
            }

            // Oppdater totalprisen i bestillingstabellen
            $sql_update_totalpris = "UPDATE bestilling SET totalpris = ? WHERE idbestilling = ?";
            $stmt_update_totalpris = $conn->prepare($sql_update_totalpris);
            if ($stmt_update_totalpris) {
                $stmt_update_totalpris->bind_param("di", $totalpris, $last_inserted_id);
                $stmt_update_totalpris->execute();
                $stmt_update_totalpris->close();
            } else {
                echo "Feil: Kunne ikke oppdatere totalprisen.";
            }

            // Hent avdelingsinformasjon
            $sql_avdeling = "SELECT navn, penger_start, penger_brukt FROM avdeling WHERE idavdeling = ?";
            $stmt_avdeling = $conn->prepare($sql_avdeling);
            $stmt_avdeling->bind_param("i", $kostnadsted);
            $stmt_avdeling->execute();
            $result_avdeling = $stmt_avdeling->get_result();
            $avdeling = $result_avdeling->fetch_assoc();
            $stmt_avdeling->close();

            // Oppdater penger_brukt i avdelingstabellen
            $ny_penger_brukt = $avdeling['penger_brukt'] + $totalpris;
            $sql_update_avdeling = "UPDATE avdeling SET penger_brukt = ? WHERE idavdeling = ?";
            $stmt_update_avdeling = $conn->prepare($sql_update_avdeling);
            if ($stmt_update_avdeling) {
                $stmt_update_avdeling->bind_param("di", $ny_penger_brukt, $kostnadsted);
                $stmt_update_avdeling->execute();
                $stmt_update_avdeling->close();
            } else {
                echo "Feil: Kunne ikke oppdatere penger_brukt.";
            }

            // Beregn gjenværende beløp på kontoen
            $penger_igjen = $avdeling['penger_start'] - $ny_penger_brukt;

            // Del opp dato og klokkeslett
            $leveringsdato = date("Y-m-d", strtotime($bestillingsdato));
            $leveringsklokkeslett = date("H:i", strtotime($bestillingsdato));

            // Generer kvittering
            echo '
            <h3>Din bestilling er nå registrert på '.$navn.' for den '.$leveringsdato.' klokken '.$leveringsklokkeslett.'.</h3>
            <p>En epost med bekreftelse på dette har også blitt sendt til '.$email.'.</p>
            <p>Ved spørsmål eller endringer i etterkant, ta kontakt med kantineleder Jens; tlf: 40 38 28 39, eller jens.andeberg@osloskolen.no.';

            echo '<h4>Kostnadssted belastet: '.$avdeling['navn'].'</h4>';
            echo '<p>Gjenværende beløp på kontoen: '.$penger_igjen.' kr</p>';

            echo '<table border="1">
                <tr>
                    <th>Produktnavn</th>
                    <th>Pris per enhet</th>
                    <th>Antall</th>
                    <th>Total pris</th>
                </tr>';

            foreach ($bestilte_varer as $vare) {
                $total_pris_vare = $vare['pris'] * $vare['antall'];
                echo '<tr>
                    <td>'.$vare['navn'].'</td>
                    <td>'.$vare['pris'].' kr</td>
                    <td>'.$vare['antall'].'</td>
                    <td>'.$total_pris_vare.' kr</td>
                </tr>';
            }

            echo '<tr>
                <td colspan="3"><strong>Totalpris</strong></td>
                <td><strong>'.$totalpris.' kr</strong></td>
            </tr>
            </table>';

        } else {
            // Spørringen feilet
            echo "Feil: Kunne ikke legge til bestilling.";
        }
    } else {
        // Kunne ikke forberede spørringen
        echo "Feil: Kunne ikke forberede spørringen.";
    }
} else {
    echo "Feil forespørselsmetode.";
}
?>
