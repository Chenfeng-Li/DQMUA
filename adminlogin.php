 <?php
session_start();
require_once('pdo.php');

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
  // If we have no POST data

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['name']) && isset($_POST['pass']) ) {
    unset($_SESSION['admin']);

        $check = hash('md5', $salt.$_POST['pass']);

        $stmt = $pdo->prepare("SELECT * FROM admin where name = :xyz");
        $stmt->execute(array(":xyz" => $_POST['name']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stored_hash=$row['password'];

        if ( $check == $stored_hash ) {
            // Redirect the browser to game.php
            $_SESSION['admin'] = $_POST['name'];
            $_SESSION['success'] = "登录成功";
            error_log("Login success ".$_POST['name']);
            header("Location: index.php");
            return;
        } else {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['failure'] = "登录失败";
            header('Location: index.php');
            return;
        }
    
}
?>




<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8'>
		<meta name = ‘viewport’ content=‘width=device-width, initial-scale=1’>
		<title>管理员登录</title>
		<link rel='icon' type='image/png' href='image/icon/DQMUA.png'>

		<script src="https://kit.fontawesome.com/4dd2f70620.js" crossorigin="anonymous"></script>
		<link rel="stylesheet" href='css/style.css'>
		<script src="js/script.js"></script>


	</head>
<body>

<div class="container">
<h2>管理员登录</h2>
<form method="POST">
<label for="nam" >账户</label>
<input type="text" name="name" id="nam"><br/>
<label for="id_1723">密码</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>
</body>
