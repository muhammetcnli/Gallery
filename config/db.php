<?php
// config/db.php

function getDB() {
    try {
        // Standard MongoDB driver connection
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
        return $manager;
    } catch (Exception $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Helper to insert one document (no framework, kept minimal)
function insertOne($collection, $document) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert($document);
    $manager = getDB();
    $manager->executeBulkWrite("fotogaleri.$collection", $bulk);
}

// Helper to fetch documents
function findAll($collection, $filter = [], $options = []) {
    $manager = getDB();
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery("fotogaleri.$collection", $query);
    return $cursor;
}

function countDocuments($collection, $filter = []) {
    $manager = getDB();
    $command = new MongoDB\Driver\Command([
        'count' => $collection,
        'query' => $filter,
    ]);
    $cursor = $manager->executeCommand('fotogaleri', $command);
    $result = current($cursor->toArray());
    return (int)($result->n ?? 0);
}

function deleteOne($collection, $filter) {
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->delete($filter, ['limit' => 1]);
    $manager = getDB();
    $manager->executeBulkWrite("fotogaleri.$collection", $bulk);
}
