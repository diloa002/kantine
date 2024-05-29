<?php
include "db.con.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Funksjoner for å legge til, oppdatere og slette elementer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        $action = $_POST["action"];
        
        if ($action == "add") {
            $navn = $_POST["navn"];
            $innhold = $_POST["innhold"];
            $pris = $_POST["pris"];
            $kategori = $_POST["kategori"];

            $sql = "INSERT INTO meny (navn, innhold, pris, kategori) VALUES ('$navn', '$innhold', '$pris', '$kategori')";
            if ($conn->query($sql) === TRUE) {
                echo "Nytt element lagt til i menyen.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if ($action == "update") {
            $id = $_POST["idmeny"];
            $navn = $_POST["navn"];
            $innhold = $_POST["innhold"];
            $pris = $_POST["pris"];
            $kategori = $_POST["kategori"];

            $sql = "UPDATE meny SET navn='$navn', innhold='$innhold', pris='$pris', kategori='$kategori' WHERE idmeny=$id";
            if ($conn->query($sql) === TRUE) {
                echo "Element oppdatert.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        if ($action == "delete") {
            $id = $_POST["idmeny"];

            $sql = "DELETE FROM meny WHERE idmeny=$id";
            if ($conn->query($sql) === TRUE) {
                echo "Element slettet.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
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

// Henter alle meny elementer fra databasen
$sql_menu = "SELECT idmeny, navn, innhold, pris, kategori FROM meny";
$result_menu = $conn->query($sql_menu);

$menu_items = array();
if ($result_menu->num_rows > 0) {
    while ($row = $result_menu->fetch_assoc()) {
        $menu_items[] = $row;
    }
}

$conn->close();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Meny Administrasjon</title>
</head>
<body>
    <div>
        <h2>Administrer Meny</h2>
        <form method="post" action="">
            <h3>Legg til nytt element</h3>
            <label for="navn">Navn:</label>
            <input type="text" id="navn" name="navn" required><br><br>
            <label for="innhold">Innhold:</label>
            <input type="text" id="innhold" name="innhold" required><br><br>
            <label for="pris">Pris:</label>
            <input type="number" id="pris" name="pris" required><br><br>
            <label for="kategori">Kategori:</label>
            <select id="kategori" name="kategori" required>
                <?php
                foreach ($categories as $category_id => $category_name) {
                    echo "<option value='$category_id'>$category_name</option>";
                }
                ?>
            </select><br><br>
            <input type="hidden" name="action" value="add">
            <input type="submit" value="Legg til">
        </form>

        <h3>Eksisterende meny elementer</h3>
        <?php
        foreach ($menu_items as $item) {
            echo "<form method='post' action=''>
                <input type='hidden' name='idmeny' value='{$item['idmeny']}'>
                <label for='navn_{$item['idmeny']}'>Navn:</label>
                <input type='text' id='navn_{$item['idmeny']}' name='navn' value='{$item['navn']}' required><br><br>
                <label for='innhold_{$item['idmeny']}'>Innhold:</label>
                <input type='text' id='innhold_{$item['idmeny']}' name='innhold' value='{$item['innhold']}' required><br><br>
                <label for='pris_{$item['idmeny']}'>Pris:</label>
                <input type='number' id='pris_{$item['idmeny']}' name='pris' value='{$item['pris']}' required><br><br>
                <label for='kategori_{$item['idmeny']}'>Kategori:</label>
                <select id='kategori_{$item['idmeny']}' name='kategori' required>";
                    foreach ($categories as $category_id => $category_name) {
                        $selected = $category_id == $item['kategori'] ? "selected" : "";
                        echo "<option value='$category_id' $selected>$category_name</option>";
                    }
                echo "</select><br><br>
                <input type='hidden' name='action' value='update'>
                <input type='submit' value='Oppdater'>
            </form>
            <form method='post' action=''>
                <input type='hidden' name='idmeny' value='{$item['idmeny']}'>
                <input type='hidden' name='action' value='delete'>
                <input type='submit' value='Slett'>
            </form><br>";
        }
        ?>
    </div>
</body>
</html>
