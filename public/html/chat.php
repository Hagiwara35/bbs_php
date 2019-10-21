<?php
session_start();

if (!isset($_SESSION["user_name"])) {
    $no_login_url = "../../";
    header("Location: {$no_login_url}");
    exit;
}

$dsn = 'mysql:dbname=login_test;host=127.0.0.1';
$user = 'user';
$password = 'user';

try {
$dbh = new PDO($dsn, $user, $password);

$sql = 'select * from chat_table, user where chat_table.user_id = user.id order by chat_table.id DESC';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>チャットルーム</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/card.css">
</head>

<body>
<!--コメント送信-->
<div id="comment">
    <p id="name_print"></p>
    <textarea id="comment_area" onkeyup="sendMessage(event)" placeholder="コメント" maxlength="255"></textarea>
</div>

<!--コメント書き込み-->
<div id="chat">
    <?php
    foreach ($dbh->query($sql) as $item) {
        if ($item['id'] == $_COOKIE['userid']) {
            echo '<div class="user">'
                . '<span class="user_name">' . $item['user_name'] . '</span>'
                . '<p>' . $item['comment'] . '</p>'
                . '</div>'
                . '<div class="bms_clear"></div>';
        } else {
            echo '<div class="client">'
                . '<span class="client_name">' . $item['user_name'] . '</span>'
                . '<p>' . $item['comment'] . '</p>'
                . '</div>'
                . '<div class="bms_clear"></div>';
        }

    }

    } catch (PDOException $e) {
        print('Error:' . $e->getMessage());
        die();
    } finally {
        $dbh = null;
    }
    ?>
</div>

<script type="text/javascript" src="../js/comment.js"></script>
</body>
</html>