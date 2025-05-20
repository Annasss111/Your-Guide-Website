<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $comment = htmlspecialchars($_POST['comment']);
    $rating = (int)$_POST['rating'];
    
    // Gestion de l'upload photo
    $photoPath = null;
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = 'uploads/reviews/';
        $photoName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $photoName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $photoPath = $targetPath;
        }
    }
    
    // Sauvegarde en base de données (statut 'pending' par défaut)
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, comment, rating, photo_path, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$userId, $comment, $rating, $photoPath]);
    
    header("Location: index.php?review_submitted=1");
    exit;
} else {
    header("Location: index.php");
    exit;
}