<?php
// Start the session so the navbar knows if someone is logged in
if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}

// Default to 0 if they aren't logged in
$cart_count = 0;

// Find out exactly which page the user is currently on
$current_page = basename($_SERVER['PHP_SELF']);

// If they are logged in and the database connection exists, count their actual items
if (isset($_SESSION['user_id']) && isset($conn)) 
{
    $nav_user_id = $_SESSION['user_id'];
    
    // SUM(quantity) just in case they added multiples of the same item
    $nav_stmt = $conn->prepare("SELECT SUM(quantity) as total_items FROM cart WHERE user_id = ?");
    $nav_stmt->bind_param("i", $nav_user_id);
    $nav_stmt->execute();
    $nav_result = $nav_stmt->get_result();
    $nav_row = $nav_result->fetch_assoc();
    
    // If there are items, set the count and keep it 0.
    $cart_count = isset($nav_row['total_items']) ? $nav_row['total_items'] : 0;
}
?>
<nav class="navbar">
    <div class="logo">
        <a href="index.php" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
            <span class="logo-icon bg-yellow neo-border" style="display: flex; justify-content: center; align-items: center;">
                <i class="fa-solid fa-cube" style="font-size: 16px;"></i>
            </span> PRINT.IT
        </a>
    </div>
    
    <button class="hamburger" id="hamburger-button" style="display: flex; justify-content: center; align-items: center; font-size: 24px;">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="nav-links" id="nav-links">
        <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active-link' : ''; ?>">
            <i class="fa-solid fa-house" style="margin-right: 5px;"></i> HOME
        </a>
        <a href="marketplace.php" class="<?php echo ($current_page == 'marketplace.php') ? 'active-link' : ''; ?>">
            <i class="fa-solid fa-store" style="margin-right: 5px;"></i> MARKETPLACE
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] !== 'admin'): ?>
                <a href="upload-product.php" class="<?php echo ($current_page == 'upload-product.php') ? 'active-link' : ''; ?>">
                    <i class="fa-solid fa-upload" style="margin-right: 5px;"></i> UPLOAD PRODUCT
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="admin-overview.php" class="<?php echo ($current_page == 'admin-overview.php') ? 'active-link' : ''; ?>">
                    <i class="fa-solid fa-shield-halved" style="margin-right: 5px;"></i> ADMIN ZONE
                </a>
            <?php else: ?>
                <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active-link' : ''; ?>">
                    <i class="fa-solid fa-user" style="margin-right: 5px;"></i> USER DASHBOARD
                </a>
            <?php endif; ?>

            <a href="cart.php" class="cart-btn neo-border" style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                 <i class="fa-solid fa-cart-shopping"></i>
                 <span class="cart-badge bg-pink"><?php echo $cart_count; ?></span>
            </a>

            <a href="logout.php" class="neo-btn bg-yellow">
                <i class="fa-solid fa-right-from-bracket" style="margin-right: 5px;"></i> LOG OUT
            </a>

        <?php else: ?>
            <a href="cart.php" class="cart-btn neo-border" style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                 <i class="fa-solid fa-cart-shopping"></i>
                 <span class="cart-badge bg-pink"><?php echo $cart_count; ?></span>
            </a>

           <a href="login.php" class="neo-btn" style="background-color: #42f308; color: black; text-decoration: none;">
               <i class="fa-solid fa-right-to-bracket" style="margin-right: 5px;"></i> LOG IN
           </a>
           <a href="signup.php" class="neo-btn bg-cyan">
               <i class="fa-solid fa-user-plus" style="margin-right: 5px;"></i> SIGN UP
           </a>

        <?php endif; ?>
    </div>
</nav>