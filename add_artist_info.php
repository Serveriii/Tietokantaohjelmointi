<?php

require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_POST['artist_name'], $_POST['album_name'], $_POST['track_names'], $_POST['media_type_id'], $_POST['milliseconds'], $_POST['unitprice'])) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}
// Koodin voi testata korvaamalla $_POST-kutsut käsinkirjoitetuilla arvoilla. $_POST-metodilla haetaan arvot frontista, jota ei tässä tehtävänannossa ole olemassa.
$artist_name = $_POST['artist_name'];
$album_name = $_POST['album_name'];
$track_names = $_POST['track_names'];
$media_type_id = $_POST['media_type_id'];
$milliseconds = $_POST['milliseconds'];
$unitprice = $_POST['unitprice'];

try {
    $pdo = openDB();
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$pdo->beginTransaction();

try {

    // Add the artist
    $stmt = $pdo->prepare("INSERT INTO artists (Name) VALUES (:artist_name)");
    $stmt->execute(['artist_name' => $artist_name]);
    $artist_id = $pdo->lastInsertId();

    // Add the album
    $stmt = $pdo->prepare("INSERT INTO albums (Title, ArtistId) VALUES (:album_name, :artist_id)");
    $stmt->execute(['album_name' => $album_name, 'artist_id' => $artist_id]);
    $album_id = $pdo->lastInsertId();

    // Add the tracks
    $stmt = $pdo->prepare("INSERT INTO tracks (Name, AlbumId, MediaTypeId, Milliseconds, UnitPrice) 
                        VALUES (:track_name, :album_id, :media_type_id, :milliseconds, :unitprice)");
    foreach ($track_names as $track_name) {
    $stmt->execute([
        'track_name' => $track_name, 
        'album_id' => $album_id,
        'media_type_id' => $media_type_id,
        'milliseconds' => $milliseconds,
        'unitprice' => $unitprice
    ]);
}

    $pdo->commit();

    echo json_encode(['success' => 'Artist added']);
} catch (PDOException $e) {
    $pdo->rollback();
    echo json_encode(['error' => 'Artist not added']);
}