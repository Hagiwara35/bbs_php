// websoketオープン
var pas = "localhost";

var conn = new WebSocket('ws://' + pas + ':8080');
var name;     // ユーザーネーム
var user_json = {
    "id": undefined,
    "name": undefined,
    "message": undefined
};  // ユーザーの基本情報
var cookie_obj = new Object();

conn.onopen = function (e) {
    console.log("Connection established!");

    //クッキー情報の読み込み
    var cookie = document.cookie.split(';');
    cookie.forEach(function(item) {
        cookie_obj[item.split("=")[0].replace(/\s+/g, "")] = item.split("=")[1];
    });

    user_json['id'] = cookie_obj['userid'];
    user_json['name'] = cookie_obj['name'];
    document.getElementById('name_print').innerHTML = 'ユーザーネーム：' + cookie_obj["name"];
};

//テキストエリアにて値が入力、Enterが押された時に発火するイベント
function sendMessage(e) {   //キーコードを取得
    var code = (e.keyCode ? e.keyCode : e.which);
    //Enterの投下
    if (code !== 13) {
        return;
    }

    var content = document.getElementById('chat').innerHTML;

    //JSONデータを作成
    user_json.message = document.getElementById('comment_area').value;

    if (user_json.message.length === 0) {
        return;
    }
    //メッセージをコンソールに渡す
    conn.send(JSON.stringify(user_json));

    //初期化＋chat欄に書き込み
    document.getElementById('chat').innerHTML = '<div class=\"user\">'
        + '<span class=\"user_name\">' + user_json.name + '</span>'
        + '<p>' + user_json.message + '</p>'
        + '</div>'
        + '<div class=\"bms_clear\"></div>'
        + content;

    document.getElementById('comment_area').value = '';
};

//相手からメッセージが送られてきたときに発火するイベント
conn.onmessage = function (e) {
    console.log(e.data);

    var content = document.getElementById('chat').innerHTML;

    e = JSON.parse(e.data);

    //初期化＋chat欄に書き込み
    document.getElementById('chat').innerHTML = '<div class=\"client\">'
        + '<span class=\"client_name\">' + e.name + '</span>'
        + '<p>' + e.message + '</p>'
        + '</div>'
        + '<div class=\"bms_clear\"></div>'
        + content;
};