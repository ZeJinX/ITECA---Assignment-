<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'includes/db.php';

// Fetch all products that are IN STOCK (quantity > 0) and the username of the maker who uploaded it
$sql = "SELECT products.*, users.username 
        FROM products 
        JOIN users ON products.maker_id = users.user_id 
        WHERE products.quantity > 0 
        ORDER BY products.created_at DESC";
$result = $conn->query($sql);

// fetch all current makers 
$makers_stmt = $conn->prepare("SELECT user_id, username, bio, startup_fee, hourly_rate FROM users WHERE accepting_orders = 1 AND role != 'admin'");
$makers_stmt->execute();
$makers_result = $makers_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="marketplace-header">
        <div>
            <h1 style="font-size: 48px; font-weight: 900; text-transform: uppercase; margin-bottom: 5px;">MARKETPLACE</h1>
            <p style="font-size: 18px; font-weight: 700; color: #4B5563;">Browse services and products.</p>
        </div>
        
        <div class="search-bar">
            <form action="marketplace.php" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" class="search-input" placeholder=" Search...">
                <button type="submit" class="neo-btn bg-yellow" style="padding: 10px 15px; cursor: pointer;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>

    <h2 style="font-size: 32px; font-weight: 900; text-transform: uppercase; max-width: 1200px; margin: 40px auto 20px; padding: 0 5%;">Ready-to-Print Products</h2>

    <div class="marketplace-grid">

        <?php
        // Check if we have any products in the database
        if ($result->num_rows > 0) 
            {
            // Loop through each product row
            while($row = $result->fetch_assoc())
                 {
                ?>
                <div class="neo-card">
                    <div class="card-image-wrapper">
                        <img src="<?php echo htmlspecialchars($row['image_file_path']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <div class="card-badge bg-yellow"><?php echo strtoupper(htmlspecialchars($row['category'])); ?></div>
                    </div>
                    <div class="card-content">
                        <h2 class="card-title"><?php echo htmlspecialchars($row['product_name']); ?></h2>
                        <p class="card-user">@<?php echo htmlspecialchars($row['username']); ?></p>
                        <hr class="card-divider">
                        <div class="card-footer">
                            <span class="card-price">R<?php echo number_format($row['price'], 2); ?></span>
                            <form action="add-to-cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <button type="submit" class="add-to-cart-btn" style="cursor: pointer; padding: 5px 10px; font-size: 16px;">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                            </form>
                            </div>
                    </div>
                </div>
                <?php
            }
        } 
        else 
        {
            // If the database is empty, show this message
            echo "<h2 style='font-weight: 900; grid-column: 1 / -1; text-align: center; padding: 40px;'>No products found. Be the first to upload one!</h2>";
        }
        ?>

    </div>

    <div id="hire-a-maker" style="max-width: 1200px; margin: 60px auto; padding: 0 5%;">
        <hr style="border: 2px solid black; margin-bottom: 40px;">
        <div style="margin-bottom: 30px;">
            <h2 style="font-size: 32px; font-weight: 900; text-transform: uppercase;">Hire a Maker</h2>
            <p style="font-weight: 700; color: #4B5563;">Need something custom? Request a print from our top makers.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            
            <?php if ($makers_result->num_rows > 0): ?>
                <?php while ($maker = $makers_result->fetch_assoc()): ?>
                    
                    <div class="neo-box" style="padding: 25px; display: flex; flex-direction: column; justify-content: space-between; background-color: #fff;">
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div class="profile-icon" style="width: 50px; height: 50px; font-size: 24px; margin-right: 15px; display: flex; align-items: center; justify-content: center; background: #e2e8f0; border-radius: 50%; border: 2px solid black;">
                                    <i class="fa-solid fa-user-gear"></i>
                                </div>
                                <h2 style="font-size: 24px; font-weight: 900; margin: 0; text-transform: uppercase;">
                                    @<?php echo htmlspecialchars($maker['username']); ?>
                               </h2>
                            </div>
                            
                            <p style="font-size: 14px; font-weight: 700; color: #4B5563; margin-bottom: 20px; line-height: 1.5; height: 60px; overflow: hidden;">
                                <?php echo !empty($maker['bio']) ? htmlspecialchars(substr($maker['bio'], 0, 80)) . '...' : 'Ready to print your next big project.'; ?>
                            </p>

                            <div style="background-color: #f8fafc; border: 2px solid black; padding: 10px; margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 900; margin-bottom: 5px;">
                                    <span>STARTUP:</span>
                                    <span>R <?php echo number_format($maker['startup_fee'], 2); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 900;">
                                    <span>RATE:</span>
                                    <span>R <?php echo number_format($maker['hourly_rate'], 2); ?>/hr</span>
                                </div>
                            </div>
                        </div>

                        <a href="maker-profile.php?maker_id=<?php echo $maker['user_id']; ?>" class="neo-btn bg-cyan" style="text-align: center; text-decoration: none; display: block; width: 100%; box-sizing: border-box; padding: 15px;">
                            <i class="fa-solid fa-eye" style="margin-right: 8px;"></i> VIEW PROFILE
                        </a>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px; border: 3px dashed black;">
                    <h3 style="font-weight: 900; text-transform: uppercase;">No makers are currently accepting orders.</h3>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>