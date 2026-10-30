<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .step-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            transition: transform 0.3s ease;
        }
        .step-card:hover {
            transform: translateY(-5px);
        }
        .buyer-card {
            border-color: #00A8CC;
        }
        .maker-card {
            border-color: #B4FF36;
        }
        .step-number {
            font-size: 40px;
            font-weight: 900;
            position: absolute;
            top: -10px;
            right: -10px;
            opacity: 0.1;
        }
        .buyer-number { color: #00A8CC; }
        .maker-number { color: #64A318; } /* Slightly darker green for contrast */
    </style>
</head>
<body style="background-color: #F8FAFC; color: #334155; font-family: Arial, sans-serif;">

    <?php include 'includes/navbar.php'; ?>

    <div style="padding: 60px 20px;">
        <div style="background-color: #ffffff; border: 3px solid #0F172A; border-radius: 12px; box-shadow: 8px 8px 0px #0F172A; max-width: 900px; margin: 0 auto; padding: 60px 30px; text-align: center; position: relative; overflow: hidden;">

            <div style="position: absolute; top: -20px; left: -20px; width: 100px; height: 100px; background-color: #00A8CC; border: 3px solid #0F172A; border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -20px; right: -20px; width: 120px; height: 120px; background-color: #FF33A1; border: 3px solid #0F172A; border-radius: 50%;"></div>

            <h1 style="font-weight: 900; font-size: 50px; margin-bottom: 25px; text-transform: uppercase; color: #0F172A; position: relative; z-index: 1; line-height: 1.3;">
                HOW <span style="background-color: #B4FF36; padding: 5px 15px; border: 3px solid #0F172A; border-radius: 8px; display: inline-block; transform: rotate(-2deg); box-shadow: 4px 4px 0px #0F172A; margin: 0 10px;">PRINT.IT</span> WORKS
            </h1>
            
            <p style="font-size: 18px; font-weight: 700; color: #475569; max-width: 650px; margin: 0 auto; line-height: 1.6; position: relative; z-index: 1;">
                Whether you need a prototype manufactured or you want to monetize your idle 3D printer, we make the process completely seamless.
            </p>
            
        </div>
    </div>

    <div style="max-width: 1200px; margin: 50px auto; padding: 0 20px; display: flex; flex-wrap: wrap; gap: 40px;">

        <div style="flex: 1; min-width: 300px;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background-color: #00A8CC; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <h2 style="font-weight: 900; font-size: 28px; color: #0F172A; margin: 0;">For Buyers</h2>
            </div>

            <div class="step-card buyer-card">
                <div class="step-number buyer-number">01</div>
                <h3 style="color: #0F172A; font-weight: 700; margin-bottom: 10px;"><i class="fa-solid fa-magnifying-glass" style="color: #00A8CC; margin-right: 8px;"></i> Browse or Request</h3>
                <p style="color: #64748B; line-height: 1.5; font-size: 14px;">Explore our retail marketplace for ready-to-print items. See something you like? Add it to your cart and type your exact desired quantity directly into the input—no tedious plus or minus buttons to click. Alternatively, submit an STL file for a custom bespoke print job.</p>
            </div>

            <div class="step-card buyer-card">
                <div class="step-number buyer-number">02</div>
                <h3 style="color: #0F172A; font-weight: 700; margin-bottom: 10px;"><i class="fa-solid fa-file-invoice-dollar" style="color: #00A8CC; margin-right: 8px;"></i> Receive a Quote</h3>
                <p style="color: #64748B; line-height: 1.5; font-size: 14px;">If you requested a custom job, local Makers will review your specifications and send you a final quoted price based on material weight and print time.</p>
            </div>

            <div class="step-card buyer-card">
                <div class="step-number buyer-number">03</div>
                <h3 style="color: #0F172A; font-weight: 700; margin-bottom: 10px;"><i class="fa-solid fa-credit-card" style="color: #00A8CC; margin-right: 8px;"></i> Secure Checkout</h3>
                <p style="color: #64748B; line-height: 1.5; font-size: 14px;">Proceed to our encrypted PayFast checkout gateway. Your funds are processed securely, and the Maker is immediately notified to begin production on your order.</p>
            </div>
        </div>

        <div style="flex: 1; min-width: 300px;">
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                <div style="width: 50px; height: 50px; background-color: #B4FF36; color: #0F172A; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <h2 style="font-weight: 900; font-size: 28px; color: #0F172A; margin: 0;">For Makers</h2>
            </div>

            <div class="step-card maker-card">
                <div class="step-number maker-number">01</div>
                <h3 style="color: #0F172A; font-weight: 700; margin-bottom: 10px;"><i class="fa-solid fa-upload" style="color: #64A318; margin-right: 8px;"></i> List Your Products</h3>
                <p style="color: #64748B; line-height: 1.5; font-size: 14px;">Upload your original STL files, set your inventory levels, and add high-quality preview images to your storefront. You control your pricing and your production schedule.</p>
            </div>

            <div class="step-card maker-card">
                <div class="step-number maker-number">02</div>
                <h3 style="color: #0F172A; font-weight: 700; margin-bottom: 10px;"><i class="fa-solid fa-clipboard-check" style="color: #64A318; margin-right: 8px;"></i> Accept Custom Jobs</h3>
                <p style="color: #64748B; line-height: 1.5; font-size: 14px;">Review incoming custom requests from Buyers. Assess their STL files, calculate your filament costs, and send them a binding quote directly through your dashboard.</p>
            </div>

            <div class="step-card maker-card">
                <div class="step-number maker-number">03</div>
                <h3 style="color: #0F172A; font-weight: 700; margin-bottom: 10px;"><i class="fa-solid fa-money-bill-wave" style="color: #64A318; margin-right: 8px;"></i> Print & Get Paid</h3>
                <p style="color: #64748B; line-height: 1.5; font-size: 14px;">Once an order clears PayFast, fire up your printer! We handle the financial logistics and automatically deduct a flat R25.00 platform fee—the rest of the profit is entirely yours.</p>
            </div>
        </div>

    </div>

    <div style="text-align: center; margin: 60px 20px; padding: 40px; background-color: #ffffff; border-radius: 12px; border: 2px dashed #CBD5E1;">
        <h2 style="font-weight: 900; color: #0F172A; margin-bottom: 15px;">Ready to get started?</h2>
        <a href="login.php" style="display: inline-block; background-color: #B4FF36; color: #0F172A; font-weight: 900; font-size: 18px; padding: 15px 30px; text-decoration: none; text-transform: uppercase; border: 2px solid #0F172A; border-radius: 4px; box-shadow: 4px 4px 0px #0F172A; transition: transform 0.1s;">
            Create Your Account
        </a>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>