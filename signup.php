<?php
// for the connection to the database 

require 'includes/db.php';
 
//error or success messsage holder

$message =""; 


if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        //will grab data from the form 
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
    

    //A security check for the hash passsword 
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //SQL query to insert data into the database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullname, $email, $hashed_password);

    //The excute and to show the message
    if ($stmt->execute()) 
        {
        $message = "<div style='background-color: #B4FF36; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-check' style='margin-right:8px;'></i> SUCCESS: Account created! You can now log in.</div>";
    } 
    else 
    {
        $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: That email might already be registered.</div>";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">

</head>
<body>
      
      <?php include 'includes/navbar.php'; ?>

      <div class="login-wrapper">
        <div class="neo-box login-box">

            <h1 style="font-weight: 900; font-size: 36px; margin-bottom: 5px; text-transform: uppercase;">Sign UP</h1>
            <p style="font-weight: 700; color: #6B7280; margin-bottom:30px; font-size: 14px;">Join the spunkiest 3D printing community!</p>

            <?php echo $message; ?>
            
            <form action="signup.php" method="POST">

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-user" style="margin-right:5px;"></i> FULL NAME</label>
                    <input type="text" name="fullname" class="neo-input" placeholder="3D Designer" required> 
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-envelope" style="margin-right:5px;"></i> EMAIL ADDRESS</label>
                    <input type="email" name="email" class="neo-input" placeholder="you@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-key" style="margin-right:5px;"></i> PASSWORD</label>
                    <input type="password" name="password" class="neo-input" placeholder="Create a password" required>
                </div>

                 <button type="submit" class="neo-btn bg-yellow w-100" style="font-size: 16px; margin-top: 10px; padding: 15px;">
                    <i class="fa-solid fa-user-plus" style="margin-right:8px;"></i> CREATE ACCOUNT
                 </button>
            </form>

            <hr class="divider">

            <p style="font-weight: 900; margin-bottom: 15px; font-size: 14px;">Already have an account?</p> 
            <a href="login.php" class="neo-btn bg-pink w-100" style="font-size: 14px; padding: 15px;">
                <i class="fa-solid fa-right-to-bracket" style="margin-right:8px;"></i> LOG IN HERE
            </a>
            
        </div>
      </div>
      <?php include 'includes/footer.php'; ?>

      <script src="js/main.js"></script>
</body>
</html>