<?php
session_start();
require 'db.php';
$message = '';

if(isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
    $stmt->bind_param("sss",$username,$email,$password);

    if($stmt->execute()){
        $_SESSION['user_id'] = $stmt->insert_id;
        header("Location: index.php");
    } else {
        $message = "Error: ".$conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup - Netflix Clone</title>
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
    <h2>Signup</h2>
    <?php if($message): ?><p class="error"><?php echo $message; ?></p><?php endif; ?>
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="signup">Sign Up</button>
    <p style="text-align:center;margin-top:10px;">Already have an account? <a href="login.php" style="color:red;text-decoration:none;">Login</a></p>
</form>
</body>
</html>
