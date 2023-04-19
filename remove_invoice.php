<?php

require_once 'connection.php';


try {
    $pdo = openDB();
} catch (PDOException $e) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}
// Koodin voi testata korvaamalla $_POST-kutsun käsinkirjoitetulla arvolla. $_POST-metodilla haetaan arvot frontista, jota ei tässä tehtävänannossa ole olemassa.
$invoice_id = $_POST['id']; 

$stmt = $pdo->prepare("DELETE FROM invoice_items WHERE InvoiceId = :invoice_id");

$stmt->execute([
    'invoice_id' => $invoice_id
]);

?>

