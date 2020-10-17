<!DOCTYPE  html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>newtest</title>
    </head>
    <body>
        <?php
        //データベースに接続
            $dsn = 'mysql:dbname=tea;host=localhost';
        	$user = 'teapot';
        	$password = 'teacup';
        	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        //テーブル作成	
        	$sql = "CREATE TABLE IF NOT EXISTS test5"
        	." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name char(32),"
            . "comment TEXT,"
        	. "date DATETIME,"
        	. "pass varchar(32)"
        	.");";
            $stmt = $pdo->query($sql);
       

        //投稿
	        if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"]) && empty($_POST["edit_number"])){
    	        $sql = $pdo -> prepare("INSERT INTO test5 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
            	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
            	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
            	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            	$name = $_POST["name"];
            	$comment = $_POST["comment"];
            	$date = date("Y/m/d H:i:s");
                $pass = $_POST["pass"];
            	$sql -> execute();
	        }
         
        
        //削除
        	if(isset($_POST["delete"]) && isset($_POST["sent_pass"])){
            	$id = $_POST["delete"];
                $sent_pass = $_POST["sent_pass"];

                $sql = 'SELECT * FROM test5 WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                $stmt->execute();
                $results = $stmt->fetchAll(); 
                foreach ($results as $row){
                    if($row['pass'] == $sent_pass && $row['id'] == $id){
                        $delete_pass = $row["pass"];
                    }
                }
                
            	$sql = 'delete from test5 where id=:id AND pass=:pass';
            	$stmt = $pdo->prepare($sql);
            	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
            	$stmt->bindParam(':pass', $sent_pass, PDO::PARAM_STR);
                $stmt->execute();
        	}
               
        //編集するデータを取得
        	if(isset($_POST["edit"]) && isset($_POST["sent_pass"])){
            	$id = $_POST["edit"];
            	$sent_pass = $_POST["sent_pass"];
                $sql = 'SELECT * FROM test5 WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(); 
                foreach ($results as $row){
                    if($row['pass'] == $sent_pass && $row['id'] == $id){
                    $edit_id = $row['id'];
                	$edit_name = $row['name'];
                	$edit_comment = $row['comment'];
                    $edit_pass = $row['pass'];
                    }
                }
            }
         
        //編集
            if(!empty($_POST["edit_number"]) && !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["pass"])){
            	$id = $_POST["edit_number"];
            	$name = $_POST["name"];
            	$comment = $_POST["comment"]; 
            	$date = date("Y/m/d H:i:s");
            	$pass = $_POST["pass"];
            	$sql = 'UPDATE test5 SET name=:name,comment=:comment, date=:date, pass=:pass WHERE id=:id';
            	$stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            	$stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        ?>
        
        <!--フォーム作成-->
        <form action="new.php" method="post">
        【投稿フォーム】<br>
            名前：　　　　<input type="text" name="name" value="<?php if(!empty($_POST["edit"]) && !empty($_POST["sent_pass"])){ 
                                                                if($row['pass'] == $sent_pass && $row['id'] == $id){
                                                                    echo $edit_name; 
                                                                } 
                                                              } ?>"><br>
            コメント：　　<input type="text" name="comment" value="<?php if(!empty($_POST["edit"]) && !empty($_POST["sent_pass"])){
                                                                        if($row['pass'] == $sent_pass && $row['id'] == $id){
                                                                            echo $edit_comment; 
                                                                        }
                                                                    } ?>"><br>
            パスワード：　<input type="password" name="pass" value="<?php if(!empty($_POST["edit"]) && !empty($_POST["sent_pass"])){ 
                                                                         if($row['pass'] == $sent_pass && $row['id'] == $id){   
                                                                            echo $edit_pass; 
                                                                        }
                                                                     } ?>">
            <input type="hidden" name="edit_number" value="<?php if(!empty($_POST["edit"]) && !empty($_POST["sent_pass"])){ 
                                                                    if($row['pass'] == $sent_pass && $row['id'] == $id){
                                                                         echo $edit_id; 
                                                                    }
                                                                 } ?>"><br>
            <input type="submit" name="submit">
        </form>
                
        <form action="new.php" method="post">
        <br>【削除フォーム】<br>
            削除対象番号：<input type="number" name="delete"><br>
            パスワード：　<input type="password" name="sent_pass"><br>
            <input type="submit" value="削除">
        </form>
                
        <form action="new.php" method="post">
        <br>【編集フォーム】<br>
            編集対象番号：<input type="number" name="edit"><br>
            パスワード：　<input type="password" name="sent_pass"><br>
            <input type="submit" value="編集">
        </form>
        

        <?php
        //エラー表示
            $bar = "<br>!-------------------!<br>";
            //投稿
            if(isset($_POST["name"])){
                if($_POST["name"] == ""){
                    echo $bar . "<br>Error: Name is empty.<br>" . $bar;
                }elseif($_POST["comment"] == ""){
                    echo $bar . "<br>Error: Comment is empty.<br>" . $bar;
                }elseif(empty($_POST["pass"])){
                    echo $bar . "<br>Error: Password is empty.<br>" . $bar;
                }
            }

            //削除
            if(isset($_POST["delete"])){
                if($_POST["delete"] == ""){
                    echo $bar . "<br>Error: Number is empty.<br>" . $bar;
                }elseif($row['id'] != $id){
                    echo $bar . "<br>Error: Number is invalid.<br>" . $bar;
                }elseif(empty($sent_pass)){
                    echo $bar . "<br>Error: Password is empty.<br>" . $bar;
                }elseif($row['pass'] != $sent_pass){
                    echo $bar . "<br>Error: Password is invalid.<br>" . $bar;
                }
            }
           

            //編集
            if(isset($_POST["edit"])){
                if($_POST["edit"] == ""){
                    echo $bar . "<br>Error: Number is empty.<br>" . $bar;
                }elseif($row['id'] != $id){
                    echo $bar . "<br>Error: Number is invalid.<br>" . $bar;
                }elseif(empty($sent_pass)){
                    echo $bar . "<br>Error: Password is empty.<br>" . $bar;
                }elseif($row['pass'] != $sent_pass){
                    echo $bar . "<br>Error: Password is invalid.<br>" . $bar; 
                }
            }
        
        ?>
         
        <br>---------------------------------<br>
        <br>【投稿一覧】<br>        
        <?php
        //ブラウザ表示
            $sql = 'SELECT * FROM test5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].',';
                echo $row['date'].'<br>';
            echo "<hr>";
            } 

        ?>
        
            
    </body>
</html>