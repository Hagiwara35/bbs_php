<?php
require_once "../../vendor/autoload.php";

use src\DB\DBAccess;

session_start();

$error_message = '';
$sth = null;

if (isset($_POST['user_create'])) {
    try {
        $dbh = new DBAccess();
        $sth = $dbh->getSQLExecution(
            'select * from user where user_name = :user_name',
            [
                ':user_name' => $_POST['user_name']
            ]
        );

        if ($sth->rowCount() == 0 && preg_match('/^[0-9A-Za-z]+$/', $_POST['user_name'])) {
            $nickname;
            if ($_POST['nickname'] === '') {
                $nickname = $_POST['user_name'];
            } else {
                $nickname = $_POST['nickname'];
            }

            $dbh->getSQLExecution(
                'INSERT INTO user (id, user_name, password, nickname) VALUES (:id, :user_name, :password, :nickname)',
                [
                    ':id' => uniqid('', true),
                    ':user_name' => $_POST['user_name'],
                    ':password' => hash('ripemd160', $_POST['plain_password']),
                    ':nickname' => $nickname
                ]
            );

            $login_success_url = '../../index.php';
            header("Location: {$login_success_url}");
            exit;
        }
    } catch (PDOException $e) {
        $error_message = 'Error:DB接続エラー';
    } finally {
        $dbh = null;
        if (!$error_message) {
            if($sth->rowCount() != 0){
                $error_message = "<br>※既にこのユーザ名は使われています。";
            }else{
                $error_message = "<br>※ユーザ名　パスワードは半角英数字のみ登録できます。";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/index.css">
    <title>ユーザ作成画面</title>
</head>
<body>
<article>
        <h1 class="font-position title">CT チャット</h1>

        <section>
            <form id="login" action="user-registration.php" method="POST">
                <h2 class="font-position login-title">ユーザ作成画面</h2>

                <div class="text-card">
                    <input class="text-card-box" type="text" name="user_name" required="required">
                    <p class="font-position">ユーザ名：</p>
                </div>

                <div class="text-card">
                    <input class="text-card-box" type="text" name="nickname">
                    <p class="font-position">ニックネーム：</p>
                </div>

                <div class="text-card">
                    <input class="text-card-box" type="password" name="plain_password" required="required">
                    <p class="font-position">パスワード：</p>
                </div>

                <button class="button" name="user_create">作成</button>
                <a class="button" href="../../">
                    <p>戻る</p>
                </a>

                <p class="error-text">
                    <?php echo $error_message; ?>
                </p>
            </form>
        </section>
    </article>
</body>
</html>