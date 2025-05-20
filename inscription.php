<?php
include 'header.php';
require_once 'db_connect.php'; // Include database connection

$signupMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $birthday = trim($_POST['birthday'] ?? '');
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $password = password_hash(trim($_POST['password'] ?? ''), PASSWORD_DEFAULT);

    if ($first_name && $last_name && $email && $phone && $password) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, birthday, gender, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $phone, $birthday, $gender, $password]);
            $signupMessage = "Sign-up successful, $first_name! You can now make reservations.";
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['first_name'] = $first_name;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $signupMessage = "Failed to register. Please try again.";
        }
    } else {
        $signupMessage = "Please fill in all required fields.";
    }
}
?>

<section class="signup-hero" style="background-image: url('https://th.bing.com/th/id/OIP.1z2z7z8z9z0z1z2z3z4z5z6z7z8z9z0z?pid=ImgDetMain');">
    <div class="hero-overlay">
        <h1 class="hero-title">Join Your Tunisian Adventure</h1>
        <p class="hero-subtitle">Sign Up to Explore the Beauty of Tunisia</p>
    </div>
</section>

<section class="signup-section">
    <div class="signup-content">
        <?php if ($signupMessage): ?>
            <div class="message success" id="success-message">
                <h3>🎉 Welcome Aboard!</h3>
                <p><?php echo htmlspecialchars($signupMessage); ?></p>
                <a href="index.php" class="back-btn">Back to Destinations</a>
            </div>
        <?php else: ?>
            <form method="POST" action="inscription.php" class="signup-form">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="+216 12 345 678" required>
                </div>

                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" name="birthday">
                </div>

                <div class="checkbox-container">
                    <label><input type="radio" name="gender" value="female"> Female</label>
                    <label><input type="radio" name="gender" value="male"> Male</label>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                </div>

                <button type="submit" class="submit-btn">Sign Up Now</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
