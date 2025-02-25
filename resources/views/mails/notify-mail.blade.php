<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notification Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            text-align: center;
            padding: 20px 0;
            background-color: #007bff;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }

        .email-body {
            padding: 20px;
            font-size: 16px;
            color: #333333;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-table th,
        .order-table td {
            border: 1px solid #dddddd;
            padding: 10px;
            text-align: left;
        }

        .order-table th {
            background-color: #007bff;
            color: white;
        }

        .email-footer {
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #777777;
            border-top: 1px solid #dddddd;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 600px) {
            .email-container {
                width: 100%;
                padding: 10px;
            }

            .order-table th,
            .order-table td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>

    <div class="email-container">
        <div class="email-header">
            Order Confirmation
        </div>
        <div class="email-body">
            <p>Dear {{user.name}},</p>
            <p>Thank you for your order. Below are your order details:</p>

            <h3>Order Information</h3>
            <p><strong>Order ID:</strong> #123456</p>
            <p><strong>Order Date:</strong> February 25, 2025</p>

            <h3>Order Details</h3>
            <table class="order-table">
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
                <tr>
                    <td>Product A</td>
                    <td>2</td>
                    <td>$20.00</td>
                </tr>
                <tr>
                    <td>Product B</td>
                    <td>1</td>
                    <td>$15.00</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td><strong>$35.00</strong></td>
                </tr>
            </table>

            <p>Click the button below to view your order:</p>
            <p><a href="#" class="btn">View Order</a></p>

            <p>Thank you,<br> The Team</p>
        </div>
        <div class="email-footer">
            © 2025 Your Company. All rights reserved.
        </div>
    </div>

</body>

</html>
