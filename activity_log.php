<?php
// activity_log.php

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

// Fetch the user's activity log
$activityLog = array();
$fetchActivityLogQuery = "SELECT * FROM activity_log WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 15";
$result = $conn->query($fetchActivityLogQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $activityLog[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>Activity Log</h1>

<?php if (empty($activityLog)) : ?>
    <p>No activity found.</p>
<?php else : ?>
    <ul>
        <?php foreach ($activityLog as $activity) : ?>
            <li>
                <strong><?php echo $activity['date']; ?></strong> - <?php echo $activity['action']; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>