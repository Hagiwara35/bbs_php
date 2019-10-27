<?php
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
    <h1>ログインできたよ！！</h1>
</body>
</html>