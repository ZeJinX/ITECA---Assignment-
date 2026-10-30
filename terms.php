<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - PRINT.IT</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: #F8FAFC; color: #334155;">

    <?php include 'includes/navbar.php'; ?>

    <div style="max-width: 800px; margin: 50px auto; padding: 40px; background: #ffffff; border: 2px solid #E2E8F0; border-radius: 8px;">
        
        <h1 style="font-weight: 900; font-size: 32px; color: #0F172A; margin-bottom: 10px; text-transform: uppercase;">Terms & Conditions</h1>
        <p style="color: #64748B; margin-bottom: 30px;">Last Updated: <?php echo date("F d, Y"); ?></p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">1. Introduction</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">Welcome to PRINT.IT. By accessing our platform, you agree to these terms. PRINT.IT operates as an intermediary marketplace connecting individuals seeking 3D printing services ("Buyers") with independent 3D printer owners ("Makers").</p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">2. User Roles & Responsibilities</h3>
        <ul style="line-height: 1.6; margin-bottom: 20px; padding-left: 20px;">
            <li><strong>Buyers:</strong> Must provide accurate shipping details and clear specifications for custom jobs. Once a custom print job is accepted and printing begins, orders cannot be cancelled.</li>
            <li><strong>Makers:</strong> Must ensure they have the physical capacity, materials, and hardware required to fulfill accepted orders in a timely manner. Makers are solely responsible for the physical quality of the printed item.</li>
        </ul>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">3. Intellectual Property (IP)</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">Makers warrant that any STL files, 3D models, or digital assets uploaded to the PRINT.IT retail marketplace are their original creations, or they hold the explicit commercial license to manufacture and sell them. PRINT.IT assumes no liability for copyright or trademark infringement initiated by users.</p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">4. Payments & Platform Fees</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">All payments are securely processed via the PayFast API gateway. PRINT.IT deducts a flat platform fee of R25.00 ZAR per completed transaction to cover hosting, maintenance, and gateway logistics. Maker payouts are calculated as the total order amount minus this platform fee.</p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">5. Limitation of Liability</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">PRINT.IT is an intermediary platform and does not physically manufacture, handle, or ship the products. We are not liable for physical defects, delayed shipping, or injuries resulting from the use of 3D printed items purchased through the platform. Disputes regarding product quality must be resolved directly between the Buyer and the Maker.</p>
        
        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">6. Account Suspension</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">Platform Administrators reserve the right to suspend or terminate user accounts immediately and without prior notice if a user violates these terms, engages in fraudulent activity, or repeatedly fails to fulfill orders.</p>

    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>