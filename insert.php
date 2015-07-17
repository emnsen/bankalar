<?php

require 'config.php';
require 'bankalar.php';

$messages = [];

try {

    $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    array_push($messages, [
        'class' => 'alert alert-success',
        'message' => 'Veritabanına bağlandıldı.'
    ]);

    $tableExists = $db->query('SHOW TABLES LIKE "'.DB_TABLE_NAME.'"')->rowCount();


    if ($tableExists) {
        array_push($messages, [
            'class' => 'alert alert-warning',
            'message' => '"'.DB_TABLE_NAME.'" tablosu mevcut'
        ]);
    } else {
        // sql to create table
        $sql = "CREATE TABLE IF NOT EXISTS ".DB_TABLE_NAME." (
                id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(128) NOT NULL,
                address VARCHAR(255),
                telephone VARCHAR(16),
                fax VARCHAR(16),
                web VARCHAR(64),
                telex VARCHAR(32),
                eft VARCHAR(4),
                swift VARCHAR(16)
                )ENGINE=MyISAM DEFAULT CHARSET=utf8";
        $db->exec($sql);
        array_push($messages, [
            'class' => 'alert alert-success',
            'message' => '"'.DB_TABLE_NAME.'" isimli tablo oluşturuldu.'
        ]);
    }

    $insert_query = $db->prepare("INSERT INTO ".DB_TABLE_NAME." SET
                            name = :name,
                            address = :address,
                            telephone = :telephone,
                            fax = :fax,
                            web = :web,
                            telex = :telex,
                            eft = :eft,
                            swift = :swift
    ");
    $count  = 0;

    foreach($banks as $bank)
    {
        $query = $db->query("SELECT * FROM ".DB_TABLE_NAME." WHERE name = '".$bank['name']."'");
        if ( $query->rowCount() ){
            array_push($messages, [
                'class' => 'alert alert-warning',
                'message' => '"'.$bank['name'].'" isimli banka mevcut olduğu için oluşturulmadı.'
            ]);
        }
        else{

            $insert = $insert_query->execute([
                "name" => $bank['name'],
                "address" => $bank['address'],
                "telephone" => $bank['telephone'],
                "fax" => $bank['fax'],
                "web" => $bank['web'],
                "telex" => $bank['telex'],
                "eft" => $bank['eft'],
                "swift" => $bank['swift'],
            ]);
            if ( $insert ){
                $count++;
                array_push($messages, [
                    'class' => 'alert alert-success',
                    'message' => '"'.$bank['name'].'" isimli banka '.$db->lastInsertId().' id ile oluşturuldu.'
                ]);
            }

        }
    }

    if($count)
    {
        array_push($messages, [
            'class' => 'alert alert-success',
            'message' => 'Toplam '.$count.' adet kayıt oluşturuldu.'
        ]);
    }


} catch (PDOException $e) {
    array_push($messages, [
        'class' => 'alert alert-danger',
        'message' => 'Sorry.. Database Problem! Error' . $e->getMessage()
    ]);
}

?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <title>Insert Banks</title>
</head>
<body>
<div class="container">
    <?php
    foreach ($messages as $message) {
        echo '<div class="' . $message['class'] . '">' . $message['message'] . '</div>';
    }
    ?>
</div>
</body>
</html>
