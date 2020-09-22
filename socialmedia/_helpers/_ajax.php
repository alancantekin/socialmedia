<?php
    require("_init.inc.php");
    
    // navigate to the function which has to be called
    switch($_POST['functiontocall']){
        case "deleteCommentAjax":
            header("Location: _delete.php?commentid=".$_POST['comment_id']);
        break;
        case "likePostAjax":
            likePostAjax();
        break;
        case "hasUserLikedPostAjax":
            hasUserLikedPostAjax();
        break;
        case "commentPostAjax":
            commentPostAjax();
        break;
        case "getCommentsByIdAjax":
            getCommentsByIdAjax();
        break;
        case "getCommentIdAjax":
            getCommentIdAjax();
        break;
        default:
            die("Not a valid function!");
        break;
    }

    function likePostAjax(){
        require("_db_connect.inc.php");
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        $date_created = date("Y-m-d H:i:s");
        
        // prepare statement
        $statement = !hasUserLikedPost($post_id, $user_id) ? 
            $pdo->prepare("INSERT INTO likes (user_id, post_id, date_created) VALUES (:pUser_Id, :pPost_Id, :pDate_Created)") : 
            $pdo->prepare("DELETE FROM likes WHERE post_id = :pPost_Id AND user_id = :pUser_Id");
        $statement->bindParam(':pUser_Id', $user_id, PDO::PARAM_INT);
        $statement->bindParam(':pPost_Id', $post_id, PDO::PARAM_INT);
        // only bind param date created if user hasn't liked yet!
        if(!hasUserLikedPost($post_id, $user_id)) { $statement->bindParam(':pDate_Created', $date_created, PDO::PARAM_STR); }

        if(!$statement->execute()){ // if satatement didn't execute successfully
            echo("Couldn'd like/dislike post!");
        }
    }

    function hasUserLikedPostAjax(){
        echo "<returnValue>";
        if(hasUserLikedPost($_POST['post_id'], $_POST['user_id'])){
            echo "true";
        }
        else{
            echo "false";
        }
        echo "</returnValue>";
    }
    
    function commentPostAjax(){
        require("_db_connect.inc.php");
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        $text = $_POST['text'];
        $date_created = date("Y-m-d H:i:s");

        // prepare statement
        $statement = $pdo->prepare("INSERT INTO comment (user_id, post_id, text, date_created) VALUES (:pUser_Id, :pPost_Id, :pText, :pDate_Created)");
        $statement->bindParam(':pUser_Id', $user_id, PDO::PARAM_INT);
        $statement->bindParam(':pPost_Id', $post_id, PDO::PARAM_INT);
        $statement->bindParam(':pText', $text, PDO::PARAM_STR);
        $statement->bindParam(':pDate_Created', $date_created, PDO::PARAM_STR);
        
        if(!$statement->execute()){ // if satatement didn't execute successfully
            echo("Couldn'd comment post!");
        }
    }

    function getCommentsByIdAjax(){
        echo "<returnValue>".getCommentsByPostId($_POST['post_id'])."</returnValue>";
    }

    function getCommentIdAjax(){
        require("_db_connect.inc.php");
        $maxId;
        foreach ($pdo->query("SELECT MAX(id) FROM comment") as $row){
            $maxId = $row[0];
        }
        echo "<returnValue>".$maxId."</returnValue>";
    }
?>