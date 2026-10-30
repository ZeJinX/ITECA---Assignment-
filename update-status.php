<?php
session_start();
require 'includes/db.php';

// This is a security check where the user mustbe logged in and submitting a form
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') 
{
    header("Location: dashboard.php");
    exit();
}

$job_id = intval($_POST['job_id']);
$new_status = $_POST['status'];
$maker_id = $_SESSION['user_id']; 

// Update the database, ensuring only the assigned maker can change the status
$stmt = $conn->prepare("UPDATE print_jobs SET status = ? WHERE job_id = ? AND maker_id = ?");
$stmt->bind_param("sii", $new_status, $job_id, $maker_id);

if ($stmt->execute()) 
{
    header("Location: dashboard.php?msg=status_updated");
} 
else 
{
    header("Location: dashboard.php?msg=error");
}
exit();
?>
