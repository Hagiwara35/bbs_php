<?php
require_once 'vendor/autoload.php';

use src\DB\DBAccess;

session_start();

$error_message = '';
$sth = null;

if (isset($_POST['login'])) {
    try {
        $sth = (new DBAccess())->getSQLExecution(
                'select * from user where user_name = :user_name and password = :password',
                [
                        ':user_name' => $_POST['user_name'],
                        ':password' => hash('ripemd160', $_POST['password'])
                ]
        );

        if ($sth->rowCount() == 1) {
            $sth = $sth->fetch(PDO::FETCH_ASSOC);

            // js用
            setcookie('user_id', $sth['id']);
            setcookie('user_name', $_POST['user_name']);

            // php用
            $_SESSION['user_id'] = $sth['id'];
            $_SESSION['user_name'] = $_POST['user_name'];

            $login_success_url = 'public/html/sled-list.php';
            header("Location: {$login_success_url}");
            exit;
        }
    } catch (PDOException $e) {
        $error_message = 'Error:DB接続エラー';
    } finally {
        $dbh = null;
        if(!$error_message){
            $error_message = '<br>※ID、もしくはパスワードが間違っています。<br>　もう一度入力して下さい。';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ログイン画面</title>
</head>

<body>
    <h1>ログイン画面</h1>
    <div>
        <h1>
            <?php
            if (isset($_SESSION['user_name'])) {
                echo "{$_SESSION['user_name']}でログイン中...";
            }
            ?>
        </h1>
    </div>

    <div>
        <form action="index.php" method="POST">
            <p>ユーザ名：<input type="text" name="user_name"></p>
            <p>パスワード：<input type="password" name="password"></p>
            <input type="submit" name="login" value="ログイン" style="float: left; margin: 0 10px 0 0">
        </form>
        <a href="public/html/user-registration.php">
            <button>ユーザを新規作成</button>
        </a>
    </div>

    <div>
        <?php
        if (isset($_SESSION['user_name'])) {
            echo "<a href=\"public/html/logout.php\"><button>ログアウト</button></a>";
        }

        if ($error_message) {
            echo $error_message;
        }
        ?>
    </div>
</body>

</html>