<?php
session_start();
require_once 'db_connect.php';

// Vérifier si l'utilisateur est connecté avec les bons identifiants
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['username'] !== 'admin') {
    // Traiter le formulaire de connexion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Vérifier les identifiants
        if ($username === 'admin' && $password === '1234') {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username; // Stocker le nom d'utilisateur dans la session
            header("Location: admin.php");
            exit;
        } else {
            $login_error = "Accès refusé : nom d'utilisateur ou mot de passe incorrect. Utilisez 'admin' et '1234'.";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Connexion</title>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="admin.css">
    </head>
    <body class="login">
        <div class="login-container">
            <h2>Connexion Admin</h2>
            <?php if (isset($login_error)): ?>
                <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>
            <form class="login-form" method="POST">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit" name="login">Se connecter</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Code existant pour la gestion des destinations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['destination-name'])) {
    $name = trim($_POST['destination-name']);
    $image = trim($_POST['destination-image']);
    $link = trim($_POST['destination-link']);

    if ($name && $image && $link) {
        $destinationsFile = 'destinations.json';
        $destinations = [];
        if (file_exists($destinationsFile)) {
            $jsonContent = file_get_contents($destinationsFile);
            if ($jsonContent === false) {
                error_log("Failed to read destinations.json in admin.php");
                header("Location: admin.php?error=4");
                exit;
            }
            $destinations = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error in admin.php: " . json_last_error_msg());
                header("Location: admin.php?error=2");
                exit;
            }
        }
        $destinations[] = [
            "name" => $name,
            "image" => $image,
            "link" => $link
        ];
        if (!file_put_contents($destinationsFile, json_encode($destinations, JSON_PRETTY_PRINT))) {
            error_log("Failed to write to destinations.json in admin.php");
            header("Location: admin.php?error=3");
            exit;
        }
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($destinationsFile, true);
        }
        header("Location: admin.php?success=1");
        exit;
    } else {
        header("Location: admin.php?error=1");
        exit;
    }
}

// Gestion des actions sur les avis (accepter/rejeter)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_action'])) {
    $reviewId = (int)$_POST['review_id'];
    $action = $_POST['review_action'];

    if ($reviewId && in_array($action, ['accept', 'reject'])) {
        try {
            $status = $action === 'accept' ? 'accepted' : 'rejected';
            $stmt = $pdo->prepare("UPDATE reviews SET status = ? WHERE id = ?");
            $stmt->execute([$status, $reviewId]);
            header("Location: admin.php?success=2");
            exit;
        } catch (PDOException $e) {
            error_log("Error updating review status: " . $e->getMessage());
            header("Location: admin.php?error=5");
            exit;
        }
    }
}

// Charger les destinations
$destinationsFile = 'destinations.json';
$destinations = [];
if (file_exists($destinationsFile)) {
    $jsonContent = file_get_contents($destinationsFile);
    if ($jsonContent === false) {
        error_log("Failed to read destinations.json for display in admin.php");
    } else {
        $destinations = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error in admin.php display: " . json_last_error_msg());
            $destinations = [];
        }
    }
}

// Charger les avis en attente et acceptés
$pendingReviews = [];
$acceptedReviews = [];
try {
    $stmt = $pdo->query("SELECT id, author, text, image, rating FROM reviews WHERE status = 'pending'");
    $pendingReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT author, text, image, rating FROM reviews WHERE status = 'accepted'");
    $acceptedReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching reviews: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Destinations and Reviews</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>Admin - Manage Destinations and Reviews</h1>
        <a href="index.php" style="color: white; position: absolute; top: 1rem; left: 1rem;">Back to Home</a>
        <a href="admin.php?logout=1" class="logout-link">Déconnexion</a>
    </header>
    <div class="admin-container">
        <?php if (isset($_GET['success'])): ?>
            <div class="message success">
                <?php echo $_GET['success'] == 1 ? "Destination added successfully!" : "Review updated successfully!"; ?>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="message error">
                <?php
                switch ($_GET['error']) {
                    case 1:
                        echo "Please fill in all fields.";
                        break;
                    case 2:
                        echo "Error reading destinations file. Please check the JSON format.";
                        break;
                    case 3:
                        echo "Error saving destination. Please check file permissions.";
                        break;
                    case 4:
                        echo "Failed to access destinations file.";
                        break;
                    case 5:
                        echo "Error updating review status.";
                        break;
                    default:
                        echo "An unexpected error occurred.";
                }
                ?>
            </div>
        <?php endif; ?>
        <form class="admin-form" method="POST">
            <h2>Add a New Destination</h2>
            <input type="text" name="destination-name" placeholder="Destination Name" required>
            <input type="url" name="destination-image" placeholder="Image URL" required>
            <input type="url" name="destination-link" placeholder="Link URL" required>
            <button type="submit">Add Destination</button>
        </form>
        <div class="destination-list">
            <?php if (empty($destinations)): ?>
                <p>No destinations found.</p>
            <?php else: ?>
                <p>Displaying <?php echo count($destinations); ?> destinations.</p>
                <?php foreach ($destinations as $destination): ?>
                    <a href="<?php echo htmlspecialchars($destination['link']); ?>" class="destination-item" aria-label="Visiter <?php echo htmlspecialchars($destination['name']); ?>">
                        <img src="<?php echo htmlspecialchars($destination['image']); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>">
                        <div class="destination-overlay"><?php echo htmlspecialchars($destination['name']); ?></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <h2>Pending Reviews</h2>
        <div class="review-list">
            <?php if (empty($pendingReviews)): ?>
                <p>No pending reviews.</p>
            <?php else: ?>
                <p>Displaying <?php echo count($pendingReviews); ?> pending reviews.</p>
                <?php foreach ($pendingReviews as $review): ?>
                    <div class="review-item">
                        <img src="<?php echo htmlspecialchars($review['image']); ?>" alt="Review Image">
                        <div class="review-overlay"><?php echo htmlspecialchars($review['author']); ?></div>
                        <p style="padding: 0.5rem;"><?php echo htmlspecialchars($review['text']); ?></p>
                        <p style="padding: 0.5rem;">Rating: <?php echo str_repeat('★', $review['rating']); ?></p>
                        <form method="POST" class="review-form">
                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                            <div class="review-actions">
                                <button type="submit" name="review_action" value="accept" class="accept-btn">Accept</button>
                                <button type="submit" name="review_action" value="reject" class="reject-btn">Reject</button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <h2>Accepted Reviews</h2>
        <div class="accepted-review-list">
            <?php if (empty($acceptedReviews)): ?>
                <p>No accepted reviews.</p>
            <?php else: ?>
                <p>Displaying <?php echo count($acceptedReviews); ?> accepted reviews.</p>
                <?php foreach ($acceptedReviews as $review): ?>
                    <div class="accepted-review-item">
                        <img src="<?php echo htmlspecialchars($review['image']); ?>" alt="Review Image">
                        <div class="accepted-review-overlay"><?php echo htmlspecialchars($review['author']); ?></div>
                        <p style="padding: 0.5rem;"><?php echo htmlspecialchars($review['text']); ?></p>
                        <p style="padding: 0.5rem;">Rating: <?php echo str_repeat('★', $review['rating']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>