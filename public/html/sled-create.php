<?php
require_once "../../vendor/autoload.php";

use src\DB\DBAccess;

session_start();

if (!isset($_SESSION["user_name"])) {
    $no_login_url = "../../";
    header("Location: {$no_login_url}");
    exit;
}

$error_message = '';

if (isset($_POST['sled_create'])) {
    try {
        $sth = (new DBAccess())->getSQLExecution(
            "INSERT INTO sled_table (id, sled_name, create_at, create_user_id) VALUES (NULL, :sled_name, CURRENT_TIMESTAMP, :create_user_id)",
            [
                ':sled_name' => $_POST['sled_name'],
                ':create_user_id' => $_SESSION['user_id']
            ]
        );

        $login_success_url = 'sled-list.php';
        header("Location: {$login_success_url}");
        exit;
    } catch (PDOException $e) {
        $error_message = 'Error:DB接続エラー';
    } finally {
        $dbh = null;
        if (!$error_message) {
            $error_message = "<br>※ID、もしくはパスワードが間違っています。<br>　もう一度入力して下さい。";
        }
    }
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>スレッド作成画面</title>
</head>
<body>
<h1>スレッド作成画面</h1>

<form action="sled-create.php" method="POST">
    <p>スレッド名：<input type="text" name="sled_name"></p>
    <input type="submit" value="スレッドを作成" name="sled_create">
</form>
<?php
echo $error_message;
?>
</body>
</html>