<!DOCTYPE html>

<html>

    <head>
        <meta charset="utf-8">
        <title>Mission5-1</title>
        <link rel="stylesheet" href="style.css">
    </head>

    <body>

    <h1>オススメのお店を教えてください！！</h1>

    <hr>
    
    <?php

    $edit_name = "";
    $edit_comment = "";
    $edit_pass = "";
    $hidden_number = "";

    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    // テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "posttime TEXT,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);


    // 1.新規送信フォーム

    // 送信がされた場合
    if(isset($_POST["submitbutton"]) && empty($_POST["editing"]) ){
        
        // (1)名前もコメントもパスワードも入力されなかった時
        if ( empty($_POST["name"]) && empty($_POST["comment"]) && empty($_POST["pass"]) ) {
            
            echo "<p> 入力してください<hr> </p>";
        
        // (2)名前は入力された & コメント・パスワードがなかった時
        }elseif( !empty($_POST["name"]) && empty($_POST["comment"]) && empty($_POST["pass"]) ){
            
            echo "<p> コメントとパスワードを入力してください<hr> </p>";
            
        // (3)名前がない & コメント・パスワードは入力された時
        }elseif( empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) ){
            
            echo "<p> 名前を入力してください<hr> </p>";
        
        // (4)コメントが入力された＆名前とパスワードがない時
        }elseif(empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["pass"]) ){
            
            echo "<p> 名前とパスワードを入力してください<hr> </p>";
        
        // (5)コメントがない＆名前とパスワードは入力された時 
        }elseif(!empty($_POST["name"]) && empty($_POST["comment"]) && !empty($_POST["pass"]) ){
            
            echo "<p> コメントを入力してください<hr> </p>";
        
        // (6)パスワードが入力された＆名前・コメントがない時   
        }elseif(empty($_POST["name"]) && empty($_POST["comment"]) && !empty($_POST["pass"]) ){
            
            echo "<p> 名前とコメントを入力してください<hr> </p>";
        
        // (7)パスワードがない＆名前とコメントは入力された時 
        }elseif(!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["pass"]) ){
            
            echo "<p> パスワードを入力してください<hr> </p>";
            
        // (8)名前もコメントもパスワードも入力された時
        }else{

            $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, posttime, pass) VALUES (:name, :comment, :posttime, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':posttime', $posttime, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $posttime = date("Y/m/d H:i:s");
            $pass = $_POST["pass"];
            $sql -> execute();

            echo "「".$comment."」を送信しました By".$name."<hr>";
        
        }
    }


    // 2.削除フォーム
    
    // 削除が送信された場合
    if(isset($_POST['deletebutton'])){

        $delete_number = $_POST["delete"];
        $deletepass = $_POST["deletepass"];
            
        // (1)削除番号とパスワードがなかった時
        if( empty($_POST["delete"]) && empty($_POST["deletepass"]) ){
                
            echo "<p> 削除対象番号とパスワードを入力してください<hr> </p>";
            
        // (2)削除番号がある＆パスワードがない
        }elseif( !empty($_POST["delete"]) && empty($_POST["deletepass"]) ){
                
            echo " <p> パスワードを入力してください<hr> </p>";
            
        // (3)削除番号がない＆パスワードはある
        }elseif( empty($_POST["delete"]) && !empty($_POST["deletepass"]) ){
            
            echo "<p> 削除対象番号を入力してください<hr> </p>";
                
        // (2)削除番号もパスワードも入力された時
        }else{

            $sql = 'SELECT * FROM tbtest WHERE id=:id ';                // SELECT文
            $stmt = $pdo->prepare($sql);                                // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $delete_number, PDO::PARAM_INT);    // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                                           // ←SQLを実行する。
            $results = $stmt->fetchAll(); 

            foreach ($results as $row){

                $delete_password = $row['pass'];

            }

            // パスワードが一致する場合
            if( $deletepass == $delete_password ){

                $id = $_POST["delete"];
                $sql = 'delete from tbtest where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                
                //削除成功のブラウザ表示
                echo $delete_number."番のコメントを削除しました<hr>";

            }else{

                //削除失敗のブラウザ表示
                echo "<p> パスワードが違います<hr> </p>";

            }
        }
    }


    // 3.編集フォーム
    
    // (3-1)編集ボタンが押されたとき
    // 対象番号に一致する書き込みの内容をフォームに表示する
    
    if(isset($_POST["editbutton"]) ){

        $edit_number = $_POST["edit"];
        $editpass = $_POST["editpass"];

        // (1)編集対象番号とパスワードが入力されていないとき
        if( empty($_POST["edit"]) && empty($_POST["editpass"]) ){
        
            echo "<p> 編集対象番号とパスワードを入力してください<hr> </p>";
        
        // (2)編集番号は入力された＆パスワードがない
        }elseif(!empty($_POST["edit"]) && empty($_POST["editpass"]) ){
            
            echo "<p> パスワードを入力してください<hr> </p>";
        
        // (3)編集番号がない＆パスワードは入力された時
        }elseif(empty($_POST["edit"]) && !empty($_POST["editpass"]) ){
            
            echo "<p> 編集対象番号を入力してください<hr> </p>";
        
        // (4)編集番号もパスワードも入力された時
        }else{

            $sql = 'SELECT * FROM tbtest WHERE id=:id ';                // SELECT文
            $stmt = $pdo->prepare($sql);                                // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $edit_number, PDO::PARAM_INT);    // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                                           // ←SQLを実行する。
            $results = $stmt->fetchAll(); 

            foreach ($results as $row){

                $edit_password = $row['pass'];

            }

            // パスワードが一致する場合
            if( $editpass == $edit_password ){

                foreach ($results as $row){
                    
                    //一致する書き込みの内容をフォームに表示
                    //$rowの中にはテーブルのカラム名が入る
                    $hidden_number = $row['id'];
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
                    $edit_pass = $row['pass'];

                    echo $hidden_number."番のコメントを編集しています<br>";
                    echo "パスワードを変更しない場合は、パスワードの欄はそのままでOKです！<hr>";
        
                }

                //編集スタートのブラウザ表示


            }else{

                //編集失敗のブラウザ表示
                echo "<p> パスワードが違います<hr> </p>";

            }   
        }
    }
    
    // (3-2)送信された番号に合う書き込みを上書きする
    
    // hiddenボックス内が空かどうか確認して条件分岐
    // 空ではない（数字が入っている）場合は編集が実行
    if( isset($_POST["submitbutton"]) && !empty($_POST["editing"]) ){

        $id = $_POST["editing"];
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $posttime =  date("Y/m/d H:i:s");
        $pass = $_POST["pass"];
        $sql = 'UPDATE tbtest SET name=:name, comment=:comment, posttime=:posttime, pass=:pass WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':posttime', $posttime, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // 編集成功のブラウザ表示
        echo $id."番のコメントを編集しました<hr>";
        
    }


    ?>
    

    <!--送信フォーム-->
    <form action="" method="post">
        
        <div class="list">

            <ul>
                <li><input type="text" name="name" placeholder="名前" value= "<?php echo $edit_name; ?>" ></li>
                
                <li><input type="text" name="comment" placeholder="コメント" value= "<?php echo $edit_comment; ?>" ></li>
                
                <li><input type="password" name="pass" placeholder="パスワード" value= "<?php echo $edit_pass; ?>"></li>
                    
            </ul>

            <input type="submit" name="submitbutton">
            
            <!--編集中かどうかを判断するために使うフォーム-->
            <input type="hidden" name="editing" value= "<?php if(isset($_POST["editbutton"])){ echo $hidden_number; } ?>">
            
        </div>

    </form>
    
    <!--削除フォーム-->
    <form action="" method="post">

        <div class="list">

            <ul>
                <li><input type="number" name="delete" placeholder="削除対象番号"></li>
                <li><input type="password" name="deletepass" placeholder="パスワード"></li>
            </ul>

            <input type="submit" name="deletebutton" value="削除">

        </div>

    </form>
    
    <!--編集番号指定用フォーム-->
    <form action="" method="post">

        <div class="list">

            <ul>
                <li><input type="number" name="edit" placeholder="編集対象番号"></li>
                <li><input type="password" name="editpass" placeholder="パスワード"></li>
            </ul>

            <input type="submit" name="editbutton" value="編集">

        </div>

    </form>
    
    <hr>

    <?php

    // ブラウザ表示
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();

    foreach ($results as $row){

        //$rowの中にはテーブルのカラム名が入る
        echo '<div id="id">'.$row['id'].':</div>';
        echo '<div id="name">'.$row['name'].'</div>';
        echo '<div id="posttime">('.$row['posttime'].')</div><br>';
        echo '<div id="comment">'.$row['comment'].'</div><br>';

    }

    ?>
     
    </body>

</html>