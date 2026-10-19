<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if ($user['failed_attempts'] >= 3) {

            $lock_sql = "SELECT TIMESTAMPDIFF(SECOND, last_failed_login, NOW()) AS seconds_passed
                         FROM users
                         WHERE email = ?";
            $lock_stmt = $conn->prepare($lock_sql);
            $lock_stmt->bind_param("s", $email);
            $lock_stmt->execute();

            $lock_result = $lock_stmt->get_result();
            $lock_data = $lock_result->fetch_assoc();

            if ($lock_data['seconds_passed'] < 180) {
                $remaining_time = ceil((180 - $lock_data['seconds_passed']) / 60);
                $_SESSION['login_error'] = "Your account is locked due to multiple failed attempts. Please try again in {$remaining_time} minute(s).";
                $_SESSION['login_attempts'] = "Attempt 3 of 3 (Locked)";
                header("Location: index.php");
                exit();
            } else {
                // 3 minutes ကျော်ရင် reset
                $reset_sql = "UPDATE users 
                              SET failed_attempts = 0, last_failed_login = NULL 
                              WHERE email = ?";
                $reset_stmt = $conn->prepare($reset_sql);
                $reset_stmt->bind_param("s", $email);
                $reset_stmt->execute();

                $user['failed_attempts'] = 0;
            }
        }

        if (password_verify($password, $user['password'])) {

            $reset_sql = "UPDATE users 
                          SET failed_attempts = 0, last_failed_login = NULL 
                          WHERE email = ?";
            $reset_stmt = $conn->prepare($reset_sql);
            $reset_stmt->bind_param("s", $email);
            $reset_stmt->execute();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user['first_name'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit();
        } else {

            // Password မှားရင် +1
            $new_attempts = $user['failed_attempts'] + 1;

            $update_sql = "UPDATE users 
                           SET failed_attempts = ?, last_failed_login = NOW() 
                           WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("is", $new_attempts, $email);
            $update_stmt->execute();

            if ($new_attempts >= 3) {
                $_SESSION['login_error'] = "Your account is now locked for 3 minutes due to multiple failed attempts.";
                $_SESSION['login_attempts'] = "Attempt 3 of 3 (Locked)";
            } else {
                $_SESSION['login_error'] = "Incorrect password!";
                $_SESSION['login_attempts'] = "Attempt {$new_attempts} of 3";
            }

            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No account found with this email address.";
        $_SESSION['login_attempts'] = "Invalid Email";
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
