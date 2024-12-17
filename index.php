<?php
    date_default_timezone_set("Asia/Tokyo");

    $comment_array = array();
    $pdo = null;

    // データベース接続
    try {
        $pdo = new PDO('sqlite:data.db');

        // エラーモードを例外モードに設定
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo $e->getMessage();
    }

    if (!empty($_POST["submitButton"])) {
        $postDate = date("Y-m-d H:i:s");
        // 書き込まれたデータを取得
        $userName = $_POST["username"];
        $comment = $_POST["comment"];

        try {
            $stmt = $pdo->prepare("INSERT INTO 'bbs-TABLE' (username, comment, postDate) VALUES (?, ?, ?);");
            $stmt->bindValue(1, $userName, PDO::PARAM_STR);
            $stmt->bindValue(2, $comment, PDO::PARAM_STR);
            $stmt->bindValue(3, $postDate, PDO::PARAM_STR);
    
            $stmt->execute();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
        // リダイレクトして二重送信を防止
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $sql = "SELECT id,username,comment,postDate FROM 'bbs-table';";
    $comment_array = $pdo->query($sql);

    //データベースを閉じる
    $pdo = null;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2チャンネル風掲示板</title>
    <link rel="stylesheet" href="./styles/index.css">
</head>
<body>
    <h1 class="title">PHPで掲示板アプリ</h1>
    <br>
    <div class="boardWrapper">
        <section>
            <?php 
                foreach($comment_array as $comment): ?>
            <article>
                <div class="wrapper">
                    <div class="nameArea">
                        <p class="id"><?php echo $comment["id"]; ?>:</p>
                        <span>名前：</span>
                        <p class="username"><?php echo htmlspecialchars($comment["username"],ENT_QUOTES); ?></p> <!-- エスケープ処理 --->
                        <time>:<?php echo $comment["postDate"]; ?></time>
                    </div>
                    <p class="comment"><?php echo htmlspecialchars($comment["comment"],ENT_QUOTES); ?></p> <!-- エスケープ処理 --->
                </div>
            </article>
            <?php endforeach; ?>
        </section>
        <form class="formWrapper" method="post">
            <div>
                <input type="submit" value="書き込む" name="submitButton">
                <label for="">名前：</label>
                <input type="text" name="username" required>
            </div>

            <div>
                <textarea class="commentTextArea" name="comment" required></textarea>
            </div>
        </form>
    </div>
</body>
</html>