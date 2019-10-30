<?php
require_once '../../vendor/autoload.php';

use src\DB\DBAccess;

session_start();

if (!isset($_SESSION["user_name"])) {
    $no_login_url = "../../";
    header("Location: {$no_login_url}");
    exit;
}

try {
$dbh = new DBAccess();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>チャットルーム</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/card.css">
</head>

<body>
<?php
$sth = $dbh->getSQLExecution(
    'select * from sled_table where id = :sled_id',
    [
        ':sled_id' => $_GET['sled_num']
    ]
);
if ($sth->rowCount() == 1) {
    $sth = $sth->fetch(PDO::FETCH_ASSOC);
    echo "<h1>{$sth['sled_name']}</h1>";
} else {
    $error_url = 'sled-list.php';
    header("Location: {$error_url}");
    exit;
}
?>

<!--コメント書き込み-->
<div id="chat">
    <?php
    $sth = $dbh->getSQLExecution(
        'select * 
                from chat_table, user, sled_table 
                where chat_table.user_id = user.id 
                and chat_table.sled_id = sled_table.id 
                and sled_table.id = :sled_id 
                order by chat_table.id ASC',
        [
                ':sled_id' => $_GET['sled_num']
        ]
    );

    foreach ($sth as $item) {
        if ($item['user_id'] == $_COOKIE['userid']) {
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
        print('Error:DB接続エラー</br><a href="../../">TOPに戻る</a>');
        exit;
    } finally {
        $dbh = null;
    }
    ?>
</div>

<!--コメント送信-->
<div id="comment">
    <p id="name_print"></p>
    <textarea id="comment_area" onkeyup="sendMessage(event)" placeholder="コメント" maxlength="255"></textarea>
</div>

<script type="text/javascript" src="../js/comment.js"></script>
</body>
</html>