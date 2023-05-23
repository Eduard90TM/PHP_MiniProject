<?php
// register.php

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
$name = $email = $department = $password = '';
$errors = array();

// Process registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $department = sanitizeInput($_POST['department']);
    $password = $_POST['password'];

    // Validate form data
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($department)) {
        $errors['department'] = 'Department is required';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Check if the email is already registered
        $checkEmailQuery = "SELECT id FROM users WHERE email = '$email'";
        $checkEmailResult = $conn->query($checkEmailQuery);
        if ($checkEmailResult->num_rows > 0) {
            $errors['email'] = 'Email is already registered';
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $insertUserQuery = "INSERT INTO users (name, email, department, password) VALUES ('$name', '$email', '$department', '$hashedPassword')";
            if ($conn->query($insertUserQuery) === TRUE) {
                // Registration successful, redirect to login page
                redirect('login.php');
            } else {
                displayErrorMessage('Error: ' . $conn->error);
            }
        }
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Register</h1>

<!-- Registration Form -->
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
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $name; ?>">
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email; ?>">
    </div>
    <div>
        <label for="department">Department:</label>
        <input type="text" id="department" name="department" value="<?php echo $department; ?>">
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
    </div>
    <div>
        <button type="submit">Register</button>
    </div>
</form>

<?php include 'templates/footer.php'; ?>