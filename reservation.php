<?php
include 'header.php';
require_once 'db_connect.php';
require_once 'send_email_enhanced.php';

// Start session
if (!isset($_SESSION)) {
    session_start();
}

// Get user details
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($userId) {
    $stmt = $pdo->prepare("SELECT first_name, email FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if ($user) {
        $username = htmlspecialchars($user['first_name']);
        $userEmail = htmlspecialchars($user['email']);
    } else {
        $username = 'Guest';
        $userEmail = '';
    }
} else {
    $username = 'Guest';
    $userEmail = '';
}

// Load destinations
$destinationsFile = 'destinations.json';
$destinations = [];
if (file_exists($destinationsFile)) {
    $jsonContent = file_get_contents($destinationsFile);
    if ($jsonContent !== false) {
        $destinations = json_decode($jsonContent, true);
    }
}

// Get activity from URL
$activity = isset($_GET['activity']) ? htmlspecialchars($_GET['activity']) : 'Unknown Activity';

// Find destination image
$activityImage = '';
$destinationName = '';
foreach ($destinations as $dest) {
    if (stripos($activity, $dest['name']) !== false) {
        $activityImage = $dest['image'];
        $destinationName = $dest['name'];
        break;
    }
}
if (!$activityImage) {
    $activityImage = 'https://via.placeholder.com/800x400?text=' . urlencode($activity);
}

// Handle form submission
$successMessage = '';
$errorMessage = '';
$emailSent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = isset($_POST['reservation_date']) ? htmlspecialchars($_POST['reservation_date']) : '';
    $numPeople = isset($_POST['num_people']) ? (int)$_POST['num_people'] : 0;
    $cardNumber = isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : '';

    // Validation
    if ($userId && $date && $numPeople > 0 && $cardNumber) {
        try {
            // Insert reservation
            $stmt = $pdo->prepare("INSERT INTO reservations (user_id, activity, reservation_date, num_people, card_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $activity, $date, $numPeople, $cardNumber]);

            $successMessage = "$username, votre réservation pour '$activity' a été confirmée avec succès ! Un de nos guides vous contactera bientôt pour coordonner les détails.";

            // Send confirmation email
            if ($userEmail) {
                $subject = 'Confirmation de Réservation - Your Guide Tunisia';
                $message = "
                    <h2>Réservation Confirmée !</h2>
                    <p>Cher(e) $username,</p>
                    <p>Merci d'avoir réservé votre aventure avec <strong>Your Guide Tunisia</strong> !</p>
                    <p><strong>Activité :</strong> $activity</p>
                    <p><strong>Date :</strong> $date</p>
                    <p><strong>Nombre de personnes :</strong> $numPeople</p>
                    <p>Un de nos guides vous contactera bientôt pour coordonner les détails de votre voyage.</p>
                    <p>Pour toute question, contactez-nous à <a href='mailto:YourGuide@gmail.com'>YourGuide@gmail.com</a>.</p>
                    <p>Cordialement,<br>L'équipe Your Guide Tunisia</p>
                ";

                $emailOptions = [
                    'reply_to' => 'YourGuide@gmail.com'
                ];

                $result = send_email_enhanced($userEmail, $subject, $message, $emailOptions);
                if ($result === true) {
                    $emailSent = true;
                } else {
                    $successMessage .= "<br>Erreur lors de l'envoi de l'email : " . htmlspecialchars($result);
                }
            } else {
                $successMessage .= "<br>Aucune adresse email trouvée pour cet utilisateur.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errorMessage = "Échec de l'enregistrement de la réservation. Veuillez réessayer.";
        }
    } else {
        $errorMessage = "Veuillez remplir tous les champs correctement et vous connecter.";
    }
}
?>

<section class="reservation-hero" style="background-image: url('<?php echo htmlspecialchars($activityImage); ?>');">
    <div class="hero-overlay">
        <h1 class="hero-title">Réservez Votre Aventure à <?php echo htmlspecialchars($destinationName); ?></h1>
        <p class="hero-subtitle">Expérience : <span><?php echo htmlspecialchars($activity); ?></span></p>
    </div>
</section>

<section class="reservation-section">
    <div class="reservation-content">
        <?php if ($successMessage): ?>
            <div class="message success" id="success-message">
                <h3>🎉 Réservation Confirmée !</h3>
                <p><?php echo htmlspecialchars($successMessage); ?></p>
                <?php if ($emailSent): ?>
                    <p>Un email de confirmation a été envoyé à <?php echo htmlspecialchars($userEmail); ?>.</p>
                <?php endif; ?>
                <a href="index.php" class="back-btn">Retour aux Destinations</a>
            </div>
        <?php elseif ($errorMessage): ?>
            <div class="message error">
                <p><?php echo htmlspecialchars($errorMessage); ?></p>
                <a href="reservation.php?activity=<?php echo urlencode($activity); ?>" class="back-btn">Réessayer</a>
            </div>
        <?php else: ?>
            <form method="POST" action="reservation.php?activity=<?php echo urlencode($activity); ?>" class="reservation-form">
                <div class="form-group">
                    <label for="reservation_date">Date de Réservation</label>
                    <input type="date" id="reservation_date" name="reservation_date" required>
                </div>

                <div class="form-group">
                    <label for="num_people">Nombre de Personnes</label>
                    <input type="number" id="num_people" name="num_people" min="1" required>
                </div>

                <div class="form-group">
                    <label for="card_number">Numéro de Carte de Crédit</label>
                    <input type="text" id="card_number" name="card_number" placeholder="1234-5678-9012-3456" required>
                </div>

                <button type="submit" class="submit-btn">Confirmer Votre Aventure</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 },
            colors: ['#FFD700', '#00CED1', '#FF4500']
        });
    }
});
</script>

<?php include 'footer.php'; ?>