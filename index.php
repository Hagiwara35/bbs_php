<?php
session_start();

$error_message = '';
$sth = null;

if (isset($_POST['login'])) {
    $dsn = 'mysql:dbname=login_test;host=127.0.0.1';
    $user = 'user';
    $password = 'user';

    try {
        $dbh = new PDO($dsn, $user, $password);

        $sqlUsername = $_POST['user_name'];
        $sqlPassword = hash('ripemd160', $_POST['password']);

        $sql = 'select * from user where user_name = :user_name and password = :password';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':user_name', $sqlUsername);
        $sth->bindParam(':password', $sqlPassword);

        $sth->execute();

        if ($sth->rowCount() == 1) {
            $sth = $sth->fetch(PDO::FETCH_ASSOC);
            setcookie('userid', $sth['id']);
            setcookie('name', $_POST['user_name']);
            $_SESSION["user_name"] = $_POST['user_name'];
            $login_success_url = 'public/html/login_success.php';
            header("Location: {$login_success_url}");
            exit;
        }
    } catch (PDOException $e) {
        print('Error:' . $e->getMessage());
        die();
    } finally {
        $dbh = null;
    }

    $error_message = "<br>※ID、もしくはパスワードが間違っています。<br>　もう一度入力して下さい。";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div>
    <h1>
        <?php
        if (isset($_SESSION["user_name"])) {
            echo $_SESSION["user_name"] . "でログイン中...";
        }
        ?>
    </h1>
</div>

<form action="index.php" method="POST">
    <p>ログインID：<input type="text" name="user_name"></p>
    <p>パスワード：<input type="password" name="password"></p>
    <input type="submit" name="login" value="ログイン" style="float: left; margin: 0 10px 0 0">
</form>
<?php
if (isset($_SESSION["user_name"])) {
    echo "<a href=\"public/html/logout.php\"><button>ログアウト</button></a>";
}

if ($error_message) {
    echo $error_message;
}
?>
</body>
</html>