<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestillingskvittering</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Bestillingskvittering</h1>
    <table>
        <tr>
            <th>Meny</th>
            <th>Antall</th>
        </tr>
        <?php
        include "db.con.php";
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Motta data fra skjemaet
        $navn = $_POST['navn'];
        $tlfnr = $_POST['tlfnr'];
        $kostnadsted = $_POST['kostnadsted_belastes'];
        $bestillingsdato = $_POST['bestillingsdato'];
        $kommentar = $_POST['kommentar'];
        
        // Sett inn bestilling i databasen
        $sql_insert_bestilling = "INSERT INTO bestilling (email, dato_bestilt, dato_levering) VALUES ('$navn', NOW(), '$bestillingsdato')";
        if ($conn->query($sql_insert_bestilling) === TRUE) {
            $last_inserted_id = $conn->insert_id;
        } else {
            echo "Error: " . $sql_insert_bestilling . "<br>" . $conn->error;
        }
        
        // Loop gjennom menyelementene og sett inn bestilte varer
        foreach ($_POST as $key => $value) {
            if (is_numeric($key)) {
                $antall = $value;
                $sql_insert_bestilling_meny = "INSERT INTO bestilling_has_meny (bestilling_idbestilling, meny_idmeny, antall) VALUES ('$last_inserted_id', '$key', '$antall')";
                if ($conn->query($sql_insert_bestilling_meny) !== TRUE) {
                    echo "Error: " . $sql_insert_bestilling_meny . "<br>" . $conn->error;
                }
            }
        }
        
        // Vis bestilte varer i en tabell
        foreach ($_POST as $key => $value) {
            if (is_numeric($key)) {
                $sql_select_meny = "SELECT navn FROM meny WHERE idmeny = '$key'";
                $result_meny = $conn->query($sql_select_meny);
                $row_meny = $result_meny->fetch_assoc();
                echo "<tr>";
                echo "<td>{$row_meny['navn']}</td>";
                echo "<td>$value</td>";
                echo "</tr>";
            }
        }
    
        ?>
    </table>
    <p><strong>Fornavn:</strong> <?php echo $fornavn; ?></p>
    <p><strong>Telefonnummer:</strong> <?php echo $tlfnr; ?></p>
    <p><strong>Kostnadsted belastes:</strong> <?php echo $kostnadsted; ?></p>
    <p><strong>Bestillingsdato og -tidspunkt:</strong> <?php echo $bestillingsdato; ?></p>
    <p><strong>Kommentar for bestillingen:</strong> <?php echo $kommentar; ?></p>
</div>
<?php
}
?>

</body>
</html>
