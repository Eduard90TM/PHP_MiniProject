<?php
// dashboard.php

// Include the necessary files
include 'includes/db_connection.php';
include 'includes/functions.php';

// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get the user information from the session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$name = $_SESSION['name'];
$department = $_SESSION['department'];

// Fetch the logged hours for the user
$loggedHours = array();
$fetchHoursQuery = "SELECT * FROM logged_hours WHERE user_id = '$user_id'";
$result = $conn->query($fetchHoursQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $loggedHours[] = $row;
    }
}

// Calculate the total hours logged
$totalHours = 0;
foreach ($loggedHours as $hour) {
    $totalHours += $hour['hours'];
}

// Fetch the last week's logged hours for the user
$lastWeekLoggedHours = array();
$lastWeek = date('Y-m-d', strtotime('-7 days'));
$fetchLastWeekHoursQuery = "SELECT * FROM logged_hours WHERE user_id = '$user_id' AND date >= '$lastWeek'";
$result = $conn->query($fetchLastWeekHoursQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lastWeekLoggedHours[] = $row;
    }
}

// Update user information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = sanitizeInput($_POST['email']);
    $newName = sanitizeInput($_POST['name']);
    $newPassword = $_POST['password'];

    // Validate form data
    $errors = array();
    if (empty($newEmail)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($newName)) {
        $errors['name'] = 'Name is required';
    }

    if (!empty($newPassword)) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $updatePasswordQuery = "UPDATE users SET password = '$hashedPassword' WHERE id = '$user_id'";
        if ($conn->query($updatePasswordQuery) !== TRUE) {
            displayErrorMessage('Error updating password: ' . $conn->error);
        }
    }

    // If no errors, update the user's email and name in the database
    if (empty($errors)) {
        $updateUserQuery = "UPDATE users SET email = '$newEmail', name = '$newName' WHERE id = '$user_id'";
        if ($conn->query($updateUserQuery) === TRUE) {
            // Update the session variables
            $_SESSION['email'] = $newEmail;
            $_SESSION['name'] = $newName;

            displaySuccessMessage('User information updated successfully.');
        } else {
            displayErrorMessage('Error updating user information: ' . $conn->error);
        }
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Welcome, <?php echo $name; ?></h1>

<h2>Logged Hours Summary</h2>
<p>Total Logged Hours: <?php echo $totalHours; ?> hours</p>

<?php if (!empty($lastWeekLoggedHours)) : ?>
    <h3>Last Week's Logged Hours</h3>
    <ul>
        <?php foreach ($lastWeekLoggedHours as $hour) : ?>
            <li><?php echo $hour['hours']; ?> hours logged on <?php echo $hour['date']; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2>Update User Information</h2>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>">
        <?php if (isset($errors['email'])) : ?>
            <p class="error"><?php echo $errors['email']; ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">
        <?php if (isset($errors['name'])) : ?>
            <p class="error"><?php echo $errors['name']; ?></p>
        <?php endif; ?>
    </div>
    <div>
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password">
    </div>
    <div>
        <button type="submit">Update</button>
    </div>
</form>

<?php include 'templates/footer.php'; ?>