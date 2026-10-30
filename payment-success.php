<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['trans_id']))
{
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$transaction_id = $_GET['trans_id'];

// Mark as completed 
$update_stmt = $conn->prepare("UPDATE transactions SET status = 'completed' WHERE transaction_id = ? AND buyer_id = ?");
$update_stmt->bind_param("si", $transaction_id, $user_id);
$update_stmt->execute();

// Empty the cart
$empty_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$empty_stmt->bind_param("i", $user_id);
$empty_stmt->execute();

header("Location: dashboard.php");
exit();
?>