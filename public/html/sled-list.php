<?php
require_once "../../vendor/autoload.php";

use src\DB\DBAccess;

session_start();

if (!isset($_SESSION["user_name"])) {
    $no_login_url = "../../";
    header("Location: {$no_login_url}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>login_test</title>
</head>
<body>
<h1>スレッド一覧画面</h1>
<a href="sled-create.php"><button>スレッドを作成する</button></a>

<table border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>スレッド</th>
        <th>作成日時</th>
        <th>投稿者</th>
    </tr>
    <?php
    $sth = (new DBAccess())->getSQLExecution(
        'SELECT sled_table.id, sled_table.sled_name, sled_table.create_at, user.user_name FROM sled_table, user WHERE user.id = sled_table.create_user_id',
        []
    );

    foreach ($sth as $item) {
        echo <<<EOM
        <tr>
            <td>{$item['id']}</td>
            <td>
                <a href="sled-room.php?sled_num={$item['id']}">
                    {$item['sled_name']}
                </a>
            </td>
            <td>{$item['create_at']}</td>
            <td>{$item['user_name']}</td>
        </tr>
EOM;
    }
    ?>
</table>
</body>
</html>