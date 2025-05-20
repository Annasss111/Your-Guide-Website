<?php
include 'header.php';
?>

<div class="form-area">
    <div class="login">
        <h1>Reservation Form</h1>
        <p>Please fill out the form below to make a reservation.</p>
        <form method="POST" action="inscription.php">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="example@gmail.com" required>
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" placeholder="+216 12 345 678" required>
            <label for="destination">Destination:</label>
            <select id="destination" name="destination" required>
                <option value="" disabled selected>Select a destination</option>
                <?php
                $destinationsFile = 'destinations.json';
                $destinations = [];
                if (file_exists($destinationsFile)) {
                    $jsonContent = file_get_contents($destinationsFile);
                    $destinations = json_decode($jsonContent, true);
                    foreach ($destinations as $destination) {
                        echo '<option value="' . htmlspecialchars($destination['name']) . '">' . htmlspecialchars($destination['name']) . '</option>';
                    }
                }
                ?>
            </select>
            <label for="date">Travel Date:</label>
            <input type="date" id="date" name="date" required>
            <button type="submit">Submit Reservation</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>