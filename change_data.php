<?php
// change_data.php

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

// Initialize variables
$email = '';
$name = '';
$errors = array();

// Fetch the user's current data
$fetchUserQuery = "SELECT * FROM users WHERE id = '$user_id'";
$result = $conn->query($fetchUserQuery);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $email = $row['email'];
    $name = $row['name'];
}

// Process change data form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $email = $_POST['email'];
    $name = $_POST['name'];

    // Validate form data
    if (empty($email)) {
        $errors['email'] = 'Email field is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($name)) {
        $errors['name'] = 'Name field is required';
    }

    // If no errors, update the user data in the database
    if (empty($errors)) {
        $updateUserQuery = "UPDATE users SET email = '$email', name = '$name' WHERE id = '$user_id'";
        if ($conn->query($updateUserQuery) === TRUE) {
            displaySuccessMessage('User data updated successfully.');
        } else {
            displayErrorMessage('Error updating user data: ' . $conn->error);
        }
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Change User Data</h1>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <?php if (!empty($errors)) : ?>
        <div class="error-message">
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>">
    </div>
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">
    </div>
    <div>
        <button type="submit">Update</button>
    </div>
</form>

<?php include 'templates/footer.php'; ?>