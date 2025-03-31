<?php
session_start();
include 'db_connection.php'; // Incluez votre connexion à la base de données ici

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Redirigez vers une page de connexion ou affichez un message d'erreur
    die("Vous devez être connecté pour enregistrer votre consentement.");
}

// Récupérez l'ID utilisateur et le choix de consentement
$user_id = $_SESSION['user_id'];
$consent_type = $_POST['consent'] ?? null;

// Vérifiez que le type de consentement est valide
if (!in_array($consent_type, ['all', 'technical_only', 'decline'])) {
    die("Choix de consentement non valide.");
}

try {
    // Insérez ou mettez à jour le consentement dans la table user_cookies
    $db = new PDO($dsn, $username, $password); // Variables de connexion à la base
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour insérer le consentement
    $stmt = $db->prepare("INSERT INTO user_cookies (user_id, consent_type) VALUES (:user_id, :consent_type)
                          ON DUPLICATE KEY UPDATE consent_type = :consent_type, consent_date = NOW()");

    $stmt->execute([':user_id' => $user_id, ':consent_type' => $consent_type]);

    // Redirection après consentement
    header("Location: /votre_page_accueil.php");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
