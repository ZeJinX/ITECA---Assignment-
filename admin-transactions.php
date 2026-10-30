<?php
// Resume the user's session
session_start();
require 'includes/db.php';

// Check if they are NOT logged in, OR if their role is NOT 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') 
{
    header("Location: login.php");
    exit(); 
}

// Calculate totals only for COMPLETED jobs for custom jobs 
$fin_sql = "SELECT COUNT(*) as completed_count, SUM(estimated_price) as total_volume FROM print_jobs WHERE LOWER(status) = 'completed'";
$fin_result = $conn->query($fin_sql)->fetch_assoc();

$completed_count = $fin_result['completed_count'] ? $fin_result['completed_count'] : 0;
$jobs_volume = $fin_result['total_volume'] ? $fin_result['total_volume'] : 0;

$flat_fee = 25.00;
$jobs_revenue = $completed_count * $flat_fee;

// Calculate totals for COMPLETED/SUCCESS marketplace transactions
$market_sql = "SELECT SUM(platform_fee) as market_revenue, SUM(amount) as market_volume FROM transactions WHERE LOWER(status) IN ('completed', 'success')";
$market_result = $conn->query($market_sql)->fetch_assoc();

$market_revenue = $market_result['market_revenue'] ? $market_result['market_revenue'] : 0;
$market_volume = $market_result['market_volume'] ? $market_result['market_volume'] : 0;

// Final totals for the Dashboard Boxes
$platform_revenue = $jobs_revenue + $market_revenue;
$maker_payouts = ($jobs_volume - $jobs_revenue) + ($market_volume - $market_revenue); 

// Fetch all transactions to populate the ledger table by using Union 
$trx_sql = "
    SELECT 
        CONCAT('JOB-', pj.job_id) AS ref_id, 
        pj.estimated_price AS total_amount, 
        25.00 AS fee_taken, 
        pj.status, 
        pj.created_at, 
        buyer.username AS buyer_name, 
        maker.username AS maker_name,
        'Custom Print' as order_type
    FROM print_jobs pj
    LEFT JOIN users buyer ON pj.user_id = buyer.user_id
    LEFT JOIN users maker ON pj.maker_id = maker.user_id

    UNION ALL

    SELECT 
        t.transaction_id AS ref_id, 
        t.amount AS total_amount, 
        t.platform_fee AS fee_taken, 
        t.status, 
        t.created_at, 
        buyer.username AS buyer_name, 
        IFNULL(maker.username, 'Marketplace') AS maker_name,
        'Market Order' as order_type
    FROM transactions t
    LEFT JOIN users buyer ON t.buyer_id = buyer.user_id
    LEFT JOIN users maker ON t.maker_id = maker.user_id

    ORDER BY created_at DESC
";
$trx_result = $conn->query($trx_sql) or die("DATABASE ERROR: " . $conn->error);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <h2 class="sidebar-title">ADMIN ZONE</h2>
            <a href="admin-overview.php" class="sidebar-link"><i class="fa-solid fa-chart-line" style="margin-right: 10px;"></i> OVERVIEW</a>
            <a href="admin-users.php" class="sidebar-link"><i class="fa-solid fa-users" style="margin-right: 10px;"></i> USERS</a>
            <a href="admin-transactions.php" class="sidebar-link active"><i class="fa-solid fa-money-bill-transfer" style="margin-right: 10px;"></i> TRANSACTIONS</a>
        </aside>

        <main class="admin-main">
            <div style="margin-bottom: 40px;">
                <h1 style="font-weight: 900; font-size: 40px; text-transform: uppercase;">LEDGER</h1>
                <p style="font-weight: 700; font-size: 16px;">All marketplace financial records.</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
                <div style="background-color: #B4FF36; border: 3px solid var(--border-dark); box-shadow: 8px 8px 0px 0px var(--border-dark); padding: 30px; color: black;">
                    <div style="font-weight: 900; font-size: 14px; margin-bottom: 10px;"><i class="fa-solid fa-sack-dollar" style="margin-right: 8px;"></i>PLATFORM REVENUE (TOTAL)</div>
                    <div style="font-weight: 900; font-size: 40px;">R<?php echo number_format($platform_revenue, 2); ?></div>
                </div>
                <div style="background-color: var(--yellow); border: 3px solid var(--border-dark); box-shadow: 8px 8px 0px 0px var(--border-dark); padding: 30px; color: black;">
                    <div style="font-weight: 900; font-size: 14px; margin-bottom: 10px;"><i class="fa-solid fa-hand-holding-dollar" style="margin-right: 8px;"></i>TOTAL MAKER PAYOUTS</div>
                    <div style="font-weight: 900; font-size: 40px;">R<?php echo number_format($maker_payouts, 2); ?></div>
                </div>
            </div>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><i class="fa-solid fa-hashtag" style="margin-right: 5px;"></i> TRX ID</th>
                            <th><i class="fa-solid fa-tag" style="margin-right: 5px;"></i> TYPE</th>
                            <th><i class="fa-solid fa-user-group" style="margin-right: 5px;"></i> PARTICIPANTS</th>
                            <th><i class="fa-solid fa-coins" style="margin-right: 5px;"></i> AMOUNT</th>
                            <th><i class="fa-solid fa-bolt" style="margin-right: 5px;"></i> FEE</th>
                            <th><i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i> STATUS</th>
                            <th><i class="fa-solid fa-calendar-days" style="margin-right: 5px;"></i> DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($trx_result && $trx_result->num_rows > 0): ?>
                            <?php while($row = $trx_result->fetch_assoc()): ?>
                                <?php 
                                    $status = strtoupper($row['status']);
                                    $badge_class = 'badge-yellow'; 
                                    
                                    if (in_array($status, ['COMPLETED', 'SUCCESS'])) $badge_class = 'badge-green';
                                    if (in_array($status, ['FAILED', 'CANCELLED'])) $badge_class = 'badge-pink';
                                ?>
                                <tr>
                                    <td style="font-weight: 900;"><?php echo htmlspecialchars($row['ref_id']); ?></td>
                                    <td style="font-weight: 900; font-size: 12px; color: #00A8CC; text-transform: uppercase;">
                                        <?php echo $row['order_type']; ?>
                                    </td>
                                    <td style="font-size: 12px;">
                                        <span style="color: #64748B;"><i class="fa-solid fa-hammer" style="margin-right: 3px;"></i>M:</span> @<?php echo htmlspecialchars($row['maker_name'] ? $row['maker_name'] : 'Unknown'); ?><br>
                                        <span style="color: #64748B;"><i class="fa-solid fa-user" style="margin-right: 3px;"></i>B:</span> @<?php echo htmlspecialchars($row['buyer_name'] ? $row['buyer_name'] : 'Unknown'); ?>
                                    </td>
                                    <td style="font-size: 18px; font-weight: 900;">R<?php echo number_format($row['total_amount'], 2); ?></td>
                                    <td style="color: #22C55E; font-weight: 900;">R<?php echo number_format($row['fee_taken'], 2); ?></td>
                                    <td><span class="admin-badge <?php echo $badge_class; ?>"><?php echo $status; ?></span></td>
                                    <td style="color: #64748B; font-weight: 700;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; font-weight: 900; padding: 30px;">No transactions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
</body>
</html>