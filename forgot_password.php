<?php
session_start();
include "db.php";

$message = "";
$showReset = false;
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["check_email"])) {
        $email = trim($_POST["email"]);

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $showReset = true;
        } else {
            $message = "Email address not found.";
        }
    }

    if (isset($_POST["reset_password"])) {
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        if ($password !== $confirm_password) {
            $message = "Passwords do not match.";
            $showReset = true;
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                $message = "Password reset successfully. You can now login.";
                $showReset = false;
            } else {
                $message = "Something went wrong. Please try again.";
                $showReset = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .reset-box {
            width: 350px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: #1a1633;
            color: white;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            color: #d33;
        }

        .success {
            color: green;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #1a1633;
        }
    </style>
</head>

<body>

    <div class="reset-box">
        <h2>Reset Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!$showReset): ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" name="check_email">Continue</button>
            </form>

        <?php else: ?>

            <form method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <input type="password" name="password" placeholder="New Password" required>

                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>

                <button type="submit" name="reset_password">Reset Password</button>
            </form>

        <?php endif; ?>

        <a href="index.php" class="back">Back to Login</a>
    </div>

</body>

</html>