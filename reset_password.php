<?php
  include 'header.php';
  ?>

  <div class="form-area">
      <div class="reset-password">
          <h2>Reset Your Password</h2>
          <p>Please enter your email address to receive a password reset link.</p>
          <form action="/send-password-reset-email" method="post">
              <label for="email">Email Address:</label>
              <input type="email" id="email" name="email" placeholder="example@gmail.com" required>
              <button type="submit">Send Reset Link</button>
          </form>
          <audio controls autoplay>
              <source src="Nharek Zin-Ali Riahi - Par Jalel Benna.mp3" type="audio/mpeg">
              Your browser does not support the audio element.
          </audio>
      </div>
  </div>

  <?php include 'footer.php'; ?>