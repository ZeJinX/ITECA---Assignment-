<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRINT.IT - C2C 3D Printing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=14">
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <main class="hero">
        <div class="hero-content">
            <div class="neo-badge bg-green">
                C-2-C 3D PRINTING
            </div>
            
            <h1>PRINT<br><span>YOUR</span><br>IDEAS</h1>
            
            <p>The spunkiest peer-to-peer 3D printing marketplace. Got a printer? Make bank. Need a prototype? Find a maker.</p>
            
            <div class="hero-buttons">
                <a href="marketplace.php" class="neo-btn bg-yellow" style="font-size: 16px; padding: 15px 30px;">
                    <i class="fa-solid fa-magnifying-glass" style="margin-right: 8px;"></i> FIND A PRINTER
                </a>
                <a href="signup.php" class="neo-btn bg-cyan" style="font-size: 16px; padding: 15px 30px;">
                    <i class="fa-solid fa-bolt" style="margin-right: 8px;"></i> BECOME A MAKER
                </a>
            </div>
        </div>

        <div style="position: relative;">
            
            <div class="hero-image-box neo-box bg-cyan" style="padding: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 10px 10px 0px 0px var(--border-dark);">
                <img src="images/homeScreen.png" alt="3D Printer" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <div class="tag bg-yellow" style="position: absolute; top: 20px; right: -20px; transform: rotate(10deg); font-size: 20px; z-index: 10;">VARIOUS OPTIONS!!</div>
            <div class="tag bg-pink" style="position: absolute; bottom: 30px; left: -10px; transform: rotate(-5deg); color: white; font-size: 20px; z-index: 10;">CHEAP PRINTS</div>
            
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>