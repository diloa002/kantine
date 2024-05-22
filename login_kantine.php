<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h2>Logg inn som kantinearbeider</h2>
    <form method="POST"> 
        <label for="email">E-post:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="passord">Passord:</label><br>
        <input type="password" id="passord" name="passord" required><br><br>

        <input type="submit" value="Logg inn">
    </form>

    
<?php

include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"]=="POST") {
    session_start();

    // Sjekker om brukernavnet er gyldig
    if (empty(trim($_POST["email"]))) {
        echo "Please enter an email address.";
        echo "<a href='../loginside.php'>Try again</a>";
        exit;
    } elseif (empty(trim($_POST["passord"]))) {
        echo "Please enter a password.";
        echo "<a href='../loginside.php'>Try again</a>";
        exit;
    }

    // Gjør om fra data til variabler
    $email = trim($_POST["email"]);
    $passord = trim($_POST["passord"]);

    // SQL-spørring for å hente brukerinformasjon
    $query = "SELECT email, navn, passord, `admin` FROM bruker WHERE email = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Sjekk om brukeren ble funnet
    if ($user) {
        // Sjekk om passordet er korrekt
        if (password_verify($passord, $user['passord'])) {
            // Sjekk om brukeren er en admin
            if ($user['admin'] == 1) {
                // Start en ny økt og lagre brukerinformasjon
                $_SESSION['bruker'] = $user['navn'];
                header("location: meny.php");
                exit();
            } else {
                echo "Du har ikke tilgang til adminområdet.";
                exit();
            }
        } else {
            echo "Feil passord.";
            exit();
        }
    } else {
        echo "Bruker ikke funnet.";
        exit();
    }
}
?>

</body>
</html>
