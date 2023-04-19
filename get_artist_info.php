<?php

require_once 'connection.php';

header('Content-Type: application/json');
// Koodin voi testata korvaamalla $_GET-kutsun käsinkirjoitetulla arvolla. $_GET-metodilla haetaan arvot frontista, jota ei tässä tehtävänannossa ole olemassa.
$artist_id = $_GET['artist_id']; 

try {
    $pdo = openDB();
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}


$stmt = $pdo->prepare("SELECT Name FROM artists WHERE ArtistId = :artist_id");
$stmt->execute(['artist_id' => $artist_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['error' => 'Artist not found']);
    exit;
}

$artist = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT albums.Title AS AlbumTitle, tracks.Name AS SongName
    FROM albums
    JOIN artists ON artists.ArtistId = albums.ArtistId
    JOIN tracks ON tracks.AlbumId = albums.AlbumId
    WHERE artists.ArtistId = :artist_id
    ORDER BY albums.Title ASC, tracks.Name ASC");

$stmt->execute(['artist_id' => $artist_id]);

$albums = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $album_title = $row['AlbumTitle'];
    $track_name = $row['SongName'];
    $albums[$album_title][] = $track_name;
}

// Construct the response JSON
$response = [
    'artist_name' => $artist['Name'],
    'albums' => $albums,
];

echo json_encode($response, JSON_PRETTY_PRINT);
