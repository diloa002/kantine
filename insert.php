<?php

include "db.con.php";

// fiol
$file = 'meny_kantine.csv';
// lage en funksjon som skal kunne gjøre det slik at jeg kan velge fil som skal bli inserted.

// sjekke om den er der 
if (file_exists($file)) {
    // open
    $handle = fopen($file, 'r');

    // hvis open ordentlig
    if ($handle !== false) {
        
        // skip firstrow
        fgetcsv($handle, 5147, ",");

        // gå gjennom alle 
        while (($data = fgetcsv($handle, 5147, ",")) !== false) {
            // få data og sette dem lik den kolonnen som den tilhører
            $idmeny = $data[0];
            $navn = $data[1];
            $innhold = $data[2];
            $pris = $data[3];
            $kategori = $data[4];

            // innssert
            $sql = "INSERT INTO meny (idmeny, navn, innhold, pris, kategori) VALUES ('$idmeny', '$navn', '$kategori', '$innhold', '$pris')";

            // do the deed ykyk
            if ($conn->query($sql) === true) {
                echo "good job.<br>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        // close altså åpning av fil
        fclose($handle);

        // close kobling til database
        $conn->close();
    } else {
        echo "Failed to open file.";
    }
} else {
    echo "File not found.";
}