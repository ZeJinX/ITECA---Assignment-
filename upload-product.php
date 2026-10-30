<?php
session_start();
require 'includes/db.php';

// Check if they are logged in
if (!isset($_SESSION['user_id'])) 
{
    header("Location: login.php");
    exit(); 
}

// Locks out the admin 
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin-zone.php");
    exit(); 
}

$maker_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    // Grab all the text data
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // check if files were uploaded to avoid the HTML Ghost Button trap
    if (empty($_FILES['cover_image']['name']) || empty($_FILES['stl_file']['name'])) 
    {
        $error_msg = "Please select both images before publishing!";
    } 
    else 
    {
        // Define where the files should live 
        $image_dir = 'uploads/images/';

        // create unique names 
        $image_ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
        $new_image_name = uniqid('img_') . '.' . $image_ext;
        $image_target = $image_dir . $new_image_name;

        // the stl doesnt work so we will upload png and it will save in stl
        $file2_ext = pathinfo($_FILES['stl_file']['name'], PATHINFO_EXTENSION);
        $new_file2_name = uniqid('img2_') . '.' . $file2_ext;
        $file2_target = $image_dir . $new_file2_name;

        
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $image_target) && 
            move_uploaded_file($_FILES['stl_file']['tmp_name'], $file2_target)) 
        {
            // Save to database 
            $insert_stmt = $conn->prepare("INSERT INTO products (maker_id, product_name, category, price, description, image_file_path, stl_file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("issdsss", $maker_id, $product_name, $category, $price, $description, $image_target, $file2_target);
            
            if ($insert_stmt->execute()) 
            {
                $success_msg = "Product successfully published to the marketplace!";
            } 
            else 
            {
                $error_msg = "Database Error: " . $conn->error;
            }
        } 
        else 
        {
            $error_msg = "File upload failed. Make sure your uploads/images folder exists!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=14">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <h2 class="sidebar-title">MAKER PANEL</h2>
            <a href="dashboard.php" class="sidebar-link"><i class="fa-solid fa-layer-group" style="margin-right: 10px;"></i> MY PRINTS</a>
            <a href="settings.php" class="sidebar-link"><i class="fa-solid fa-gear" style="margin-right: 10px;"></i> SETTINGS</a>
        </aside>

        <main class="dashboard-main">
            
            <div class="page-title-row">
                <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1 style="font-weight: 900; font-size: 32px; text-transform: uppercase;">UPLOAD PRODUCT</h1>
                    <p style="font-weight: 700; color: #4B5563;">List your design on the marketplace.</p>
                </div>
            </div>

            <?php if (!empty($success_msg)): ?>
                <div style="background-color: #B4FF36; border: 3px solid black; padding: 15px; margin-bottom: 20px; font-weight: 900;">
                    <i class="fa-solid fa-check" style="margin-right: 8px;"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div style="background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; margin-bottom: 20px; font-weight: 900;">
                    <i class="fa-solid fa-triangle-exclamation" style="margin-right: 8px;"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-form-box">
                <form action="upload-product.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-tag" style="margin-right: 5px;"></i> PRODUCT NAME</label>
                        <input type="text" name="product_name" class="neo-input" placeholder="e.g. Ergonomic Laptop Stand" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-folder-open" style="margin-right: 5px;"></i> CATEGORY</label>
                            <input type="text" name="category" class="neo-input" placeholder="e.g. Office, Gaming, Tools" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-coins" style="margin-right: 5px;"></i> PRICE (ZAR)</label>
                            <input type="number" step="0.01" name="price" class="neo-input" placeholder="R 0.00" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">GALLERY IMAGE 2</label>
                            <input type="file" id="stl_input" name="stl_file" accept=".png,.jpg,.jpeg" style="display: none;" required>
                            <label for="stl_input" style="cursor: pointer; display: block;">
                                <div class="upload-area bg-cyan">
                                    <div id="stl_placeholder">
                                        <div style="font-size: 30px; margin-bottom: 5px;"><i class="fa-solid fa-image"></i></div>
                                        <p style="font-weight: 900; font-size: 14px;">Select Image 2</p>
                                        <p style="font-weight: 700; font-size: 12px; color: #333;">.png, .jpg</p>
                                    </div>
                                    <img id="stl_preview" src="" alt="Preview" style="display: none; width: 100%; max-height: 200px; object-fit: contain; border: 3px solid black; background: #fff;">
                                </div>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="form-label">COVER IMAGE</label>
                            <input type="file" id="cover_input" name="cover_image" accept=".png,.jpg,.jpeg" style="display: none;" required>
                            <label for="cover_input" style="cursor: pointer; display: block;">
                                <div class="upload-area bg-pink">
                                    <div id="cover_placeholder">
                                        <div style="font-size: 30px; margin-bottom: 5px;"><i class="fa-solid fa-camera"></i></div>
                                        <p style="font-weight: 900; font-size: 14px;">Select Photo</p>
                                        <p style="font-weight: 700; font-size: 12px; color: #333;">.png, .jpg</p>
                                    </div>
                                    <img id="cover_preview" src="" alt="Preview" style="display: none; width: 100%; max-height: 200px; object-fit: contain; border: 3px solid black; background: #fff;">
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-pen" style="margin-right: 5px;"></i> DESCRIPTION</label>
                        <textarea name="description" class="neo-input" placeholder="Describe your product, suggested print settings, layer height, etc." required></textarea>
                    </div>

                    <button type="submit" class="neo-btn bg-cyan w-100" style="padding: 15px; font-size: 16px;">
                        <i class="fa-solid fa-cloud-arrow-up" style="margin-right: 8px;"></i> PUBLISH PRODUCT
                    </button>
                </form>
            </div>
        </main>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>