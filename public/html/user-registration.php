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
            }else{
                $nickname = $_POST['nickname'];
            }

            $dbh->getSQLExecution(
                    'INSERT INTO user (id, user_name, password, nickname) VALUES (NULL, :user_name, :password, :nickname)',
                    [
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
    <title>ユーザ作成画面</title>
</head>
<body>
<h1>ユーザ作成画面</h1>
<form action="user-registration.php" method="POST">
    <p>
        ユーザ名：<input type="text" name="user_name"  required="required">
    </p>
    <p>
        ニックネーム：<input type="text" name="nickname">
    </p>
    <p>パスワード：<input type="password" name="plain_password" pattern="^[0-9A-Za-z]+$" required="required"></p>
    <input type="submit" name="user_create" value="作成">
</form>

<div>
    <?php
    if (isset($_SESSION['user_name'])) {
        echo '<a href=\'public/html/logout.php\'><button>ログアウト</button></a>';
    }

    if ($error_message) {
        echo $error_message;
    }
    ?>
</div>
</body>
</html>