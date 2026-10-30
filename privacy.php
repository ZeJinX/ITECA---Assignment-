<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - PRINT.IT</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: #F8FAFC; color: #334155;">

    <?php include 'includes/navbar.php'; ?>

    <div style="max-width: 800px; margin: 50px auto; padding: 40px; background: #ffffff; border: 2px solid #E2E8F0; border-radius: 8px;">
        
        <h1 style="font-weight: 900; font-size: 32px; color: #0F172A; margin-bottom: 10px; text-transform: uppercase;">Privacy Policy</h1>
        <p style="color: #64748B; margin-bottom: 30px;">Last Updated: <?php echo date("F d, Y"); ?></p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">1. Introduction</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">PRINT.IT ("we", "our", or "us") is committed to protecting your privacy. This policy explains how we collect, use, and safeguard your personal information when you use our marketplace, in compliance with the Protection of Personal Information Act (POPIA).</p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">2. Information We Collect</h3>
        <ul style="line-height: 1.6; margin-bottom: 20px; padding-left: 20px;">
            <li><strong>Account Data:</strong> Name, email address, and securely hashed passwords.</li>
            <li><strong>Transactional Data:</strong> Physical shipping addresses, billing details, and order histories.</li>
            <li><strong>Platform Content:</strong> STL files, product images, and custom job briefs uploaded to our servers.</li>
        </ul>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">3. Financial Data & PayFast</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">PRINT.IT does not directly collect, process, or store your credit card or banking information. All financial transactions are securely routed through the PayFast API. We only store the resulting success/failure tokens and transaction reference IDs in our database to verify your orders.</p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">4. How We Share Your Data</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">Because PRINT.IT is a Consumer-to-Consumer (C2C) marketplace, certain information must be shared to facilitate transactions:</p>
        <ul style="line-height: 1.6; margin-bottom: 20px; padding-left: 20px;">
            <li><strong>With Makers:</strong> If you are a Buyer, we share your shipping address and custom job specifications with the Maker fulfilling your order.</li>
            <li><strong>With Buyers:</strong> If you are a Maker, your public profile, contact email, and product catalogue are visible to Buyers.</li>
        </ul>
        <p style="line-height: 1.6; margin-bottom: 20px;">We do not sell, rent, or trade your personal data to external marketing agencies or third parties.</p>

        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">5. Data Security</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">We implement strict security measures to protect your data, including password hashing (bcrypt) and secure session management. However, no digital transmission is 100% secure, and we cannot guarantee absolute security against advanced cyber threats.</p>
        
        <h3 style="color: #0F172A; font-weight: 700; margin-top: 20px;">6. Your Rights</h3>
        <p style="line-height: 1.6; margin-bottom: 20px;">You have the right to request access to the personal data we hold about you, request corrections, or ask for your account and associated data to be permanently deleted from our database. To exercise these rights, please contact the platform administrator.</p>

    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>