<?php
// index.php

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
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into the database
        $sql = "INSERT INTO users (name, email, department, password) VALUES ('$name', '$email', '$department', '$hashedPassword')";
        if ($conn->query($sql) === TRUE) {
            // Registration successful, redirect to login page
            redirect('login.php');
        } else {
            displayErrorMessage('Error: ' . $conn->error);
        }
    }
}

// Fetch the activity log
$sql = "SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 15";
$result = $conn->query($sql);
$activityLog = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : array();

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Time Tracker</h1>

<!-- Registration Form -->
<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <h2>Register</h2>
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

<!-- Activity Log -->
<h2>Activity Log</h2>
<?php if (!empty($activityLog)) : ?>
    <ul>
        <?php foreach ($activityLog as $log) : ?>
            <li><?php echo $log['message']; ?></li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>No activity logged yet.</p>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>