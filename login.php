<?php
session_start();
require 'db.php';
$message = '';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user && password_verify($password,$user['password'])){
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Netflix Clone</title>
    <style>
        body{background:#141414;color:#fff;font-family:Arial;margin:0;padding:0;display:flex;justify-content:center;align-items:center;height:100vh;}
        form{background:#222;padding:30px;border-radius:10px;width:300px;}
        input{width:100%;padding:10px;margin:10px 0;border:none;border-radius:5px;}
        button{width:100%;padding:10px;background:red;color:#fff;border:none;border-radius:5px;font-weight:bold;cursor:pointer;}
        p.error{color:#ff4c4c;}
    </style>
</head>
<body>
<form method="post">
    <h2>Login</h2>
    <?php if($message): ?><p class="error"><?php echo $message; ?></p><?php endif; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="login">Login</button>
    <p style="text-align:center;margin-top:10px;">Don't have an account? <a href="signup.php" style="color:red;text-decoration:none;">Signup</a></p>
</form>
</body>
</html>
