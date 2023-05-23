<?php
// summary.php

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

// Fetch the summary of logged hours for each department and category
$summary = array();
$fetchSummaryQuery = "SELECT d.name AS department_name, c.name AS category_name, SUM(l.hours) AS total_hours
                      FROM logged_hours AS l
                      INNER JOIN departments AS d ON l.department_id = d.id
                      INNER JOIN categories AS c ON l.category_id = c.id
                      WHERE l.user_id = '$user_id'
                      GROUP BY l.department_id, l.category_id";
$result = $conn->query($fetchSummaryQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $department_name = $row['department_name'];
        $category_name = $row['category_name'];
        $total_hours = $row['total_hours'];

        if (!isset($summary[$department_name])) {
            $summary[$department_name] = array();
        }

        $summary[$department_name][$category_name] = $total_hours;
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Summary of Logged Hours</h1>

<?php if (empty($summary)) : ?>
    <p>No logged hours found.</p>
<?php else : ?>
    <?php foreach ($summary as $department_name => $categories) : ?>
        <h2><?php echo $department_name; ?></h2>
        <?php if (empty($categories)) : ?>
            <p>No logged hours for this department.</p>
        <?php else : ?>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total Hours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category_name => $total_hours) : ?>
                        <tr>
                            <td><?php echo $category_name; ?></td>
                            <td><?php echo $total_hours; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>