<?php

require_once 'connection.php';


try {
  $pdo = openDB();
} catch (PDOException $e) {
  echo json_encode(['error' => 'Connection failed']);
  exit;
}
// Koodin voi testata korvaamalla $_GET-kutsun käsinkirjoitetulla arvolla. $_GET-metodilla haetaan arvot frontista, jota ei tässä tehtävänannossa ole olemassa.
$playlist_id = $_GET['playlist_id'];

$stmt = $pdo->prepare("SELECT t.Name, t.Composer
                       FROM tracks t
                       INNER JOIN playlist_track pt ON t.TrackId = pt.TrackId
                       WHERE pt.PlaylistId = :playlist_id");

$stmt->execute([
    'playlist_id' => $playlist_id
]);


if ($stmt->rowCount() > 0) {
    $results = array();
    while ($playlist = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $results[] = array(
        'Name' => $playlist['Name'],
        'Composer' => $playlist['Composer']
      );
    }
    echo json_encode($results, JSON_PRETTY_PRINT);
  } else {
    echo "Playlist not found.";
  }
  
  ?>