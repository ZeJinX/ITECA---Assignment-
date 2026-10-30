<?php
//  ensure the user must be logged in to upload a product
if (!isset($_SESSION['user_id'])) 
    
{
    header("Location: login.php");
    exit();
}

$message = "";

// Catch the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") 
  {
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $maker_id = $_SESSION['user_id']; //This will grab the ID of the person logged in

    // Handling the Image File
    $target_dir = "uploads/images/";
    
    // Add a timestamp to the file name so two images with the same name don't overwrite each other
    $image_name = time() . "_" . basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . $image_name;
    
    // Move the file from temporary memory to the uploads/images/ folder
    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) 
    {
        
        // Temporary placeholder for the STL 3D print file 
        $stl_path = "#"; 

        // Save everything to the database
        $stmt = $conn->prepare("INSERT INTO products (maker_id, product_name, category, price, description, stl_file_path, image_file_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdsss", $maker_id, $product_name, $category, $price, $description, $stl_path, $target_file);

        if ($stmt->execute()) 
        {
            $message = "<div style='background-color: #B4FF36; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-check' style='margin-right:8px;'></i> SUCCESS: Product added to Marketplace!</div>";
        } 
        else 
        {
            $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Database failed to save.</div>";
        }
    } 
    else 
    {
        $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Failed to upload image. Make sure your uploads/images folder exists!</div>";
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div style="max-width: 600px; margin: 50px auto; padding: 40px; border: 4px solid var(--border-dark); background-color: white; box-shadow: 8px 8px 0px 0px var(--border-dark);">
        
        <h1 style="font-weight: 900; font-size: 36px; margin-bottom: 5px; text-transform: uppercase;">Upload Product</h1>
        <p style="font-weight: 700; color: #6B7280; margin-bottom: 30px; font-size: 14px;">Add a new design to the marketplace.</p>

        <?php echo $message; ?>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            
            <div style="margin-bottom: 20px;">
                <label style="font-weight: 900; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 10px;"><i class="fa-solid fa-tag" style="margin-right:5px;"></i> Product Name</label>
                <input type="text" name="product_name" class="form-input" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 900; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 10px;"><i class="fa-solid fa-folder-open" style="margin-right:5px;"></i> Category</label>
                <select name="category" class="form-input" required>
                    <option value="Tech Accessories">Tech Accessories</option>
                    <option value="Toys & Figures">Toys & Figures</option>
                    <option value="Desk Setup">Desk Setup</option>
                    <option value="Services">Services</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 900; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 10px;"><i class="fa-solid fa-coins" style="margin-right:5px;"></i> Price (ZAR)</label>
                <input type="number" step="0.01" name="price" class="form-input" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: 900; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 10px;"><i class="fa-solid fa-pen" style="margin-right:5px;"></i> Description</label>
                <textarea name="description" class="form-input" style="height: 100px; resize: none;" required></textarea>
            </div>

            <div style="margin-bottom: 35px;">
                <label style="font-weight: 900; font-size: 14px; text-transform: uppercase; display: block; margin-bottom: 10px;"><i class="fa-solid fa-camera" style="margin-right:5px;"></i> Product Image</label>
                <input type="file" name="product_image" accept="image/*" class="form-input" required>
            </div>

            <button type="submit" class="neo-btn bg-cyan w-100" style="font-size: 18px; padding: 15px;">
                <i class="fa-solid fa-cloud-arrow-up" style="margin-right:8px;"></i> UPLOAD TO MARKETPLACE
            </button>
        </form>
    </div>

    <script src="js/main.js"></script>
</body>
</html>