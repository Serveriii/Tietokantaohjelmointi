<?php

function openDB() {
    try {
        $db = new PDO('mysql:host=127.0.0.1;dbname=chinook;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
}

