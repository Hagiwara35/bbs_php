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
            setcookie('user_name', $sth['nickname']);

            // php用
            $_SESSION['user_id'] = $sth['id'];
            $_SESSION['user_name'] = $sth['nickname'];

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
    <link rel="stylesheet" href="public/css/index.css">
    <title>ログイン画面</title>
</head>

<body>
    <article>
        <h1 class="font-position title">CT チャット</h1>

        <section>
            <form id="login" action="index.php" method="POST">
                <h2 class="font-position login-title">ログイン</h2>

                <div class="text-card">
                    <input class="text-card-box" type="text" name="user_name">
                    <p class="font-position">ユーザ名</p>
                </div>

                <div class="text-card">
                    <input class="text-card-box" type="password" name="password">
                    <p class="font-position">パスワード</p>
                </div>

                <button class="button" name="login">ログイン</button>
                <a class="button" href="public/html/user-registration.php">
                    <p>アカウント作成</p>
                </a>

                <p class="error-text">
                    <?php echo $error_message; ?>
                </p>
            </form>
        </section>
    </article>
</body>

</html>