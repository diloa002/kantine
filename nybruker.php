<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<form method="POST">
        <label for="email">E-post:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="navn">Navn:</label><br>
        <input type="text" id="navn" name="navn" required><br><br>

        <label for="passord">Passord:</label><br>
        <input type="password" id="passord" name="passord" required><br><br>

        <label for="admin">Kantinearbeider?:</label>
        <input type="checkbox" name="admin" value="1"><br> 

        <input type="submit" value="Registrer">

</body>
</html>



<?php
include "nav.inc.php";

//Om POST er brukt hentes dataen fra formen og blir til variablene
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $navn = $_POST["navn"];
    $passord = $_POST["passord"];
    $admin = isset($_POST["admin"]) ? 1 : 0; // Setter admin til 1 hvis boksen er sjekket, 0 hvis ikke

      // Hasher passordet
      $options = [
        'cost' => 12
    ];
    $hashedPassword = password_hash($passord, PASSWORD_BCRYPT, $options);



    // Forbered og utfør spørringen
    $query = "INSERT INTO bruker (email, navn, passord, admin) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($query);

    // Sjekk om spørringen ble forberedt riktig
    if ($stmt) {
        // Binder parametere til spørringen og utfører den
        $stmt->bind_param("ssss", $email, $navn, $hashedPassword, $admin);
        $stmt->execute();

        // Sjekk om spørringen ble utført vellykket
        if ($stmt->affected_rows > 0) {
            // Spørringen var vellykket, gjør videre handling
            $stmt->close();
            $con->close();
            header("Location: grønn.php");
            exit();
        } else {
            // Spørringen feilet
            echo "Feil: Kunne ikke legge til bruker.";
        }
    } else {
        // Kunne ikke forberede spørringen
        echo "Feil: Kunne ikke forberede spørringen.";
    }
}
?>
