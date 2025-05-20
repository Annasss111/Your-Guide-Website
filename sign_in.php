<?php
require_once 'db_connect.php';

if (!isset($_SESSION)) {
    session_start();
}

// Handle sign-in form submission
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';

    if ($email && $password) {
        // Query the database to find the user
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                header("Location: index.php");
                exit;
            } else {
                $errorMessage = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $errorMessage = "Error: " . $e->getMessage();
        }
    } else {
        $errorMessage = "Please fill in all fields.";
    }
}

include 'header.php';
?>

<section class="signin-hero" style="background-image: url('https://th.bing.com/th/id/OIP.1z2z7z8z9z0z1z2z3z4z5z6z7z8z9z0z?pid=ImgDetMain');">
    <div class="hero-overlay">
        <h1 class="hero-title">Welcome Back!</h1>
        <p class="hero-subtitle">Sign In to Continue Your Tunisian Journey</p>
    </div>
</section>

<section class="signin-section">
    <div class="signin-content">
        <?php if ($errorMessage): ?>
            <div class="message error">
                <p><?php echo $errorMessage; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="sign_in.php" class="signin-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="submit-btn">Sign In</button>
        </form>

        <p class="signup-link">Don't have an account? <a href="inscription.php">Sign Up</a></p>
        <a href="index.php" class="back-btn">Back to Home</a>
    </div>
</section>

<?php include 'footer.php'; ?>