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

$conn->close();
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestillingsskjema</title>
</head>
<body>

<div>

<div>
    <h1>Personlig informasjon</h1>
    <form>
        <label for="fornavn">Fornavn:</label>
        <input type="text" id="fornavn" name="fornavn"><br><br>

        <label for="tlfnr">Telefonnummer:</label>
        <input type="tel" id="tlfnr" name="tlfnr"><br><br>
    </form>
</div>

<div>
    <h1>Kostnadsted som skal belastes</h1>
    <form>
        <input type="text" name="kostnadsted_belastes">
    </form>
</div>

<div>
    <h1>Bestillingsdato og -tidspunkt</h1>
    <form>
        <input type="datetime-local" name="bestillingsdato">
    </form>
</div>


    <div>
        <?php
        // Loop gjennom hver kategori
        foreach ($categories as $category_id => $category_name) {
            echo "<div>";
            echo "<h1>$category_name</h1>";

            // Henter meny elementer for denne kategorien
            include "db.con.php"; // Inkluder databasetilkoblingen igjen

            $sql_menu = "SELECT nr, meny, pris FROM meny WHERE kategori = '$category_id'";
            $result_menu = $conn->query($sql_menu);

            $menu_items = array();
            if ($result_menu->num_rows > 0) {
                while ($row = $result_menu->fetch_assoc()) {
                    $menu_items[$row["nr"]] = $row["meny"] . " - " . $row["pris"] . " kr";
                }
            }

            $conn->close();

            // Generer skjema for menyelementer i denne kategorien
            echo "<form>";
            foreach ($menu_items as $id => $name) {
                echo "<div>";
                echo "<p>$name</p>";
                echo "<input type='number' name='menu[$id]' min='0'>";
                echo "</div>";
            }
            echo "</form>";

            echo "</div>";
        }
        ?>
    </div>

    <div>
        <h1>Kommentar for bestillingen: </h1>
        <input type="text" name="kommentar">
    </div>

    <input type="submit" value="Bestill">

</div>
    
</body>
</html>
