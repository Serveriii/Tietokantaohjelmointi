<?php

require_once 'connection.php';


try {
  $pdo = openDB();
} catch (PDOException $e) {
  echo json_encode(['error' => 'Connection failed']);
  exit;
}
// Koodin voi testata korvaamalla $_POST-kutsun käsinkirjoitetulla arvolla. $_POST-metodilla haetaan arvot frontista, jota ei tässä tehtävänannossa ole olemassa.
$artist_id = $_POST['artist_id'];

try {
  $pdo->beginTransaction();

  $stmt = $pdo->prepare('DELETE FROM invoice_items WHERE TrackId IN (SELECT TrackId FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id))');
  $stmt->execute(array(':artist_id' => $artist_id));
  
  $stmt = $pdo->prepare('DELETE playlist_track  FROM playlist_track JOIN tracks ON playlist_track.TrackId = tracks.TrackId WHERE tracks.AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id)');
  $stmt->execute(array(':artist_id' => $artist_id));
  
  $stmt = $pdo->prepare('DELETE FROM tracks WHERE AlbumId IN (SELECT AlbumId FROM albums WHERE ArtistId = :artist_id)');
  $stmt->execute(array(':artist_id' => $artist_id));
  
  $stmt = $pdo->prepare('DELETE FROM albums WHERE ArtistId = :artist_id');
  $stmt->execute(array(':artist_id' => $artist_id));
  
  $stmt = $pdo->prepare('DELETE FROM artists WHERE ArtistId = :artist_id');
  $stmt->execute(array(':artist_id' => $artist_id));

  $pdo->commit();

  echo "Deleted successfully";
} catch (PDOException $e) {
  $pdo->rollBack();
  echo "Error: " . $e->getMessage();
}

?>

