<?php
// config/db.php

function getDB() {
    try {
        // Standart MongoDB Driver Bağlantısı
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        return $manager;
    } catch (Exception $e) {
        die("Veritabanı Hatası: " . $e->getMessage());
    }
}

// Veri eklemek için yardımcı fonksiyon (Framework kullanmadığımız için elle yazıyoruz)
function insertOne($collection, $document) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($document);
    $manager = getDB();
    $manager->executeBulkWrite("fotogaleri.$collection", $bulk);
}

// Veri çekmek için yardımcı fonksiyon
function findAll($collection, $filter = [], $options = []) {
    $manager = getDB();
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("fotogaleri.$collection", $query);
    return $cursor;
}
?>