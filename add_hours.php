<?php
// add_hours.php

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
$department = $_SESSION['department'];

// Initialize variables
$hours = '';
$category_id = '';
$errors = array();

// Process add hours form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $hours = $_POST['hours'];
    $category_id = $_POST['category'];

    // Validate form data
    if (empty($hours)) {
        $errors['hours'] = 'Hours field is required';
    } elseif (!is_numeric($hours)) {
        $errors['hours'] = 'Hours must be a numeric value';
    } elseif ($hours < 0) {
        $errors['hours'] = 'Hours cannot be negative';
    } elseif ($hours > 8) {
        $errors['hours'] = 'Hours cannot exceed 8';
    }

    if (empty($category_id)) {
        $errors['category'] = 'Category field is required';
    }

    // If no errors, insert the logged hours into the database
    if (empty($errors)) {
        $date = date('Y-m-d');
        $insertHoursQuery = "INSERT INTO logged_hours (user_id, department_id, category_id, hours, date) VALUES ('$user_id', '$department', '$category_id', '$hours', '$date')";
        if ($conn->query($insertHoursQuery) === TRUE) {
            displaySuccessMessage('Hours added successfully.');
        } else {
            displayErrorMessage('Error adding hours: ' . $conn->error);
        }
    }
}

// Fetch the categories for the user's department
$categories = array();
$fetchCategoriesQuery = "SELECT * FROM categories WHERE department_id = '$department'";
$result = $conn->query($fetchCategoriesQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Add Logged Hours</h1>

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
        <label for="hours">Hours:</label>
        <input type="number" id="hours" name="hours" min="0" max="8" step="0.5" value="<?php echo $hours; ?>">
    </div>
    <div>
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['id']; ?>" <?php if ($category_id === $category['id']) echo 'selected'; ?>><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <button type="submit">Add Hours</button>
    </div>
</form>

<?php include 'templates/footer.php'; ?>