<?php
// view_hours.php

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
$department = $_SESSION['department'];

// Fetch the users and their logged hours for the department
$users = array();
$fetchUsersQuery = "SELECT * FROM users WHERE department_id = '$department'";
$result = $conn->query($fetchUsersQuery);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];
        $user = array(
            'id' => $user_id,
            'name' => $row['name'],
            'logged_hours' => array()
        );

        $fetchHoursQuery = "SELECT * FROM logged_hours WHERE user_id = '$user_id'";
        $hoursResult = $conn->query($fetchHoursQuery);
        if ($hoursResult->num_rows > 0) {
            while ($hoursRow = $hoursResult->fetch_assoc()) {
                $user['logged_hours'][] = $hoursRow;
            }
        }

        $users[] = $user;
    }
}

// Close the database connection
$conn->close();
?>

<?php include 'templates/header.php'; ?>

<h1>View Hours</h1>

<?php if (empty($users)) : ?>
    <p>No users found in the department.</p>
<?php else : ?>
    <?php foreach ($users as $user) : ?>
        <h2><?php echo $user['name']; ?></h2>
        <?php if (empty($user['logged_hours'])) : ?>
            <p>No hours logged for this user.</p>
        <?php else : ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Hours</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user['logged_hours'] as $hour) : ?>
                        <tr>
                            <td><?php echo $hour['date']; ?></td>
                            <td><?php echo $hour['hours']; ?></td>
                            <td><?php echo getCategoryName($hour['category_id']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php include 'templates/footer.php'; ?>