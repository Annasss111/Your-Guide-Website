<?php
require_once 'db_connect.php';

if (function_exists('opcache_reset')) {
    opcache_reset();
}

$destinationsFile = 'destinations.json';
$destinations = [];

if (file_exists($destinationsFile)) {    //Verifie l'existence du fichier
    // Lire le contenu du fichier JSON
    // et le décoder en tableau associatif
    // Utiliser file_get_contents pour lire le fichier
    // et json_decode pour le décoder
    // Vérifier si le fichier est lisible
    $jsonContent = file_get_contents($destinationsFile);
    if ($jsonContent === false) {
        error_log("Failed to read destinations.json in index.php");
    } else {
        $destinations = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error in index.php: " . json_last_error_msg());
            $destinations = [];
        }
    }
}

// Gestion de la soumission de l'avis

$reviewMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $author = trim($_POST['author'] ?? '');
    $text = trim($_POST['text'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    if ($author && $text && $image && $rating >= 1 && $rating <= 5) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reviews (user_id, author, text, image, rating, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$userId, $author, $text, $image, $rating]);
            $reviewMessage = "Your review has been submitted and is awaiting approval.";
        } catch (PDOException $e) {
            error_log("Error inserting review: " . $e->getMessage());
            $reviewMessage = "Failed to submit review. Please try again.";
        }
    } else {
        $reviewMessage = "Please fill in all fields correctly.";
    }
}

// Récupérer les avis acceptés pour l'affichage

$acceptedReviews = [];
try {
    $stmt = $pdo->query("SELECT author, text, image, rating FROM reviews WHERE status = 'accepted'");
    $acceptedReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching reviews: " . $e->getMessage());
}

include 'header.php';
?>

<h2>Choose Your Destination:</h2>

<div class="gallery-container">
    <?php foreach ($destinations as $destination): ?>
        <a href="<?php echo htmlspecialchars($destination['link']); ?>" class="gallery-item" aria-label="Visiter <?php echo htmlspecialchars($destination['name']); ?>">
            <img src="<?php echo htmlspecialchars($destination['image']); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>">
            <div class="overlay">
                <h3><?php echo htmlspecialchars($destination['name']); ?></h3>
                <p><?php echo htmlspecialchars($destination['description'] ?? 'Discover ' . $destination['name']); ?></p>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<section class="intro-section">
    <div>
        <h2>Plan an Unforgettable Vacation in Tunisia!</h2>
        <p>Discover paradise beaches, ancient ruins, and a vibrant culture with <strong>Your Guide</strong>, created by passionate students from ENSI.</p>
        <p>From the ruins of Carthage to the pristine Mediterranean beaches, Tunisia has it all.</p>
        <p>Your adventure starts here!</p>
    </div>
    <img src="sidibou.jpg" alt="Image de Sidi Bou Saïd">
</section>

<section class="progress-section">
    <h2>Your Travel Progress</h2>
    <p>Track your journey through Tunisia!</p>
    <div class="progress-bar">
        <div class="progress" style="width: 10%;"></div>
    </div>
</section>

<section class="reviews-section">
    <h2>What our Travelers Say</h2>
    <div class="review-container" id="review-container">
        <?php foreach ($acceptedReviews as $review): ?>
            <div class="review-card">
                <img src="<?php echo htmlspecialchars($review['image']); ?>" alt="User Review">
                <div class="rating"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
                <p>"<?php echo htmlspecialchars($review['text']); ?>"</p>
                <div class="author">- <?php echo htmlspecialchars($review['author']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="submit-review-section">
    <h2>Share Your Experience</h2>
    <?php if ($reviewMessage): ?>
        <div class="message <?php echo strpos($reviewMessage, 'success') !== false ? 'success' : 'error'; ?>">
            <p><?php echo htmlspecialchars($reviewMessage); ?></p>
        </div>
    <?php endif; ?>
    <form method="POST" action="index.php" class="review-form">
        <div class="form-group">
            <label for="author">Your Name</label>
            <input type="text" id="author" name="author" placeholder="Enter your name" required>
        </div>
        <div class="form-group">
            <label for="text">Your Review</label>
            <textarea id="text" name="text" placeholder="Share your experience" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image URL</label>
            <input type="url" id="image" name="image" placeholder="Enter image URL" required>
        </div>
        <div class="form-group">
            <label for="rating">Rating</label>
            <select id="rating" name="rating" required>
                <option value="">Select rating</option>
                <option value="1">1 Star</option>
                <option value="2">2 Stars</option>
                <option value="3">3 Stars</option>
                <option value="4">4 Stars</option>
                <option value="5">5 Stars</option>
            </select>
        </div>
        <button type="submit" name="submit_review" class="submit-btn">Submit Review</button>
    </form>
</section>

<button class="scroll-top">↑</button>

<div class="video">
    <video width="640" height="360" controls autoplay muted playsinline>
        <source src="South Tour  Discover Tunisia 4K.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<?php include 'footer.php'; ?>