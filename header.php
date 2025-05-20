<?php

  if (!isset($_SESSION)) { //ken fammech session ebda wahda
      session_start();
  }

  // Check if user is logged in
  $isLoggedIn = isset($_SESSION['user_id']);
  $firstName = $isLoggedIn ? $_SESSION['first_name'] : '';

  // Handle logout
  if (isset($_GET['logout'])) {
      session_destroy();
      header("Location: index.php");
      exit;
  }
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Your Guide - Explore Tunisia</title>
      <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Old+Standard+TT:wght@400;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="styles.css">
  </head>
  <body>
      <header class="site-header">
          <div class="header-container">
              <div class="logo">
                  <img src="tunsiie.jpg" alt="Your Guide Logo" class="logo-img">
                  <h1>Your Guide</h1>
              </div>
              <button class="hamburger" aria-label="Toggle navigation">☰</button>
              <nav class="main-nav">
                  <ul>
                      <li><a href="index.php" aria-label="Page d'accueil">Home</a></li>
                      <li><a href="reservation.php" aria-label="Réserver maintenant">Reservation</a></li>
                      <li><a href="contact.php" aria-label="Nous contacter">Contact</a></li>
                      <li><a href="#accommodation" aria-label="Hébergements">Accommodation</a></li>
                  </ul>
              </nav>
              <div class="contact-info">
                  <?php if ($isLoggedIn): ?>
                      <h4>Welcome, <?php echo htmlspecialchars($firstName); ?>! <a href="?logout=1" class="logout-link">Logout</a></h4>
                  <?php else: ?>
                      <h4><a href="sign_in.php">Sign In</a> | <a href="inscription.php">Sign Up</a></h4>
                  <?php endif; ?>
              </div>
          </div>
      </header>
      <main>