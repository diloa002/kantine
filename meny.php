<?php
include "db.con.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Henter alle kategorier fra databasen
$sql_categories = "SELECT idkategori, navn FROM kategori";
$result_categories = $conn->query($sql_categories);

$categories = array();
if ($result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {
        $categories[$row["idkategori"]] = $row["navn"];
    }
}

// Henter alle avdelinger fra databasen
$sql_departments = "SELECT idavdeling, navn FROM avdeling";
$result_departments = $conn->query($sql_departments);

$departments = array();
if ($result_departments->num_rows > 0) {
    while ($row = $result_departments->fetch_assoc()) {
        $departments[$row["idavdeling"]] = $row["navn"];
    }
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestillingsskjema</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h2>Meny for kantina</h2>

<p>
    For at vi skal klare å levere bestilt mat og drikke, trenger vi litt tid på å få dette inn i logistikken vår.
    Bestillinger må derfor skje senest kl. 12.00 dagen før. Dersom bestillingen er uklar, tar vi kontakt med deg.
    <br>
    Dersom bestillingen er for over 50 personer må du henvende deg direkte til kjøkkenet for egen avtale. 
   <br>
    All mat/drikke må hentes på kjøkkenet, hvis ikke annet er avtalt. 
    Dersom du bare skal ha med deg gjester i personalkantina, registrerer du kun antall. 
    <br><br>
    Ved spørsmål eller endringer i etterkant, ta kontakt med kantineleder Jens; tlf: 40 38 28 39, eller jens.andeberg@osloskolen.no. 
</p>

<div>
    <h4>Personlig informasjon</h4>
    <form action="kvittering.php" method="POST">
        <label for="navn">Navn:</label>
        <input type="text" id="navn" name="navn" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="kostnadsted_belastes">Kostnadsted som skal belastes:</label>
        <select name="kostnadsted_belastes" required>
            <?php
            foreach ($departments as $idavdeling => $navn) {
                echo "<option value='$idavdeling'><strong>$idavdeling</strong> $navn</option>";
            }
            ?>
        </select>

        <h4>Bestillingsdato og -tidspunkt</h4>
        <input type="datetime-local" name="bestillingsdato">

</div>

    <?php
    // Loop gjennom hver kategori
    foreach ($categories as $category_id => $category_name) {
        echo "<div class='kategori'>";
        echo "<h3>$category_name</h3>";

        $sql_menu = "SELECT idmeny, navn, kategori, pris, innhold FROM meny WHERE kategori = '$category_id'";
        $result_menu = $conn->query($sql_menu);

        $menu_items = array();
        if ($result_menu->num_rows > 0) {
            while ($row = $result_menu->fetch_assoc()) {
                $menu_items[$row["idmeny"]] = array(
                    'navn' => $row["navn"],
                    'kategori' => $row["kategori"],
                    'pris' => $row["pris"],
                    'innhold' => $row["innhold"]
                );
            }
        }

        // Generer skjema for menyelementer i denne kategorien
        foreach ($menu_items as $id => $item) {
            echo "<div>";
            echo "<p class='produktnavn'>{$item['navn']} - {$item['pris']} kr</p>";
            if ($item['innhold'] != '0') {
                echo "<p> Innhold: {$item['innhold']}</p>";
            }
            echo "<input type='number' name='meny[$id]' min='0'>";
            echo "</div>";
        }

        echo "</div>";
    }
    ?>

<div class="comment">
    <label for="kommentar">Skriv de her dersom du
     har andre meldinger eller kommentarer til kantina.</label>
    <textarea id="kommentar" name="kommentar" cols="70" rows="7"></textarea></div>

<input type="submit" value="Bestill">
</form>
</div>

</body>
</html>
