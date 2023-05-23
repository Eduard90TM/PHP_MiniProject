<?php
// login.php

// Include the necessary files
include 'includes/db_connection.php';
include 'includes/functions.php';

// Initialize the session
session_start();

// Check if the user is already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Initialize variables
$email = $password = '';
$errors = array();

// Process login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    // Validate form data
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    // If no errors, proceed with login
    if (empty($errors)) {
        // Check if the email exists in the database
        $getUserQuery = "SELECT * FROM users WHERE email = '$email'";
        $getUserResult = $conn->query($getUserQuery);
        if ($getUserResult->num_rows === 1) {
            $user = $getUserResult->fetch_assoc();
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, set the session variables and redirect to dashboard
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['department'] = $user['department'];
                redirect('dashboard.php');
            } else {
                $errors['password'] = 'Invalid password';
            }
        } else {
            $errors['email'] = 'Email not found';
        }
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Login</h1>

<!-- Login Form -->
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
        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
    </div>
    <div>
        <button type="submit">Login</button>
    </div>
</form>

<?php include 'templates/footer.php'; ?>