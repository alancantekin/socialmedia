<?php
    require("_init.inc.php"); // require initial php file

    if(isset($_POST['deleteuserbutton'])){
        $user_id = getCurrentUser()->id;
        // Get all posts of this user
        $stmt = $pdo->prepare("SELECT id FROM post WHERE user_id = $user_id");
        $stmt->execute();
        // fetching rows into array
        // this is done like this because with a basic foreach loop the result would change
        // every time after a post is deleted which interrupts the foreach process
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach($result as $post_id){
            deletePost($post_id, false); // call delete post function
        }

        // Delete all likes of this user
        $statementLikes = $pdo->prepare("DELETE FROM likes WHERE user_id = :pUser_Id");
        $statementLikes->bindParam(':pUser_Id', $user_id, PDO::PARAM_INT);

        // Delete all comments of this user
        $statementComment = $pdo->prepare("DELETE FROM comment WHERE user_id = :pUser_Id");
        $statementComment->bindParam(':pUser_Id', $user_id, PDO::PARAM_INT);
        
        if($statementLikes->execute() && $statementComment->execute()){
            // Delete user itself
            $statement = $pdo->prepare("DELETE FROM user WHERE id = :pUser_Id");
            $statement->bindParam(':pUser_Id', $user_id, PDO::PARAM_INT);
            
            if($statement->execute()){
                session_destroy();
                header("Location: /socialmedia/user/login.php"); // redirect to login.php
                exit; // unsend header()
            }
            else{
                echo("Couldn'd delete account!"); // show error message
            }
        }
        else{
            echo("Couldn'd delete posts!"); // show error message
        }
    }
    else if(isset($_REQUEST['postid'])){
        deletePost($_REQUEST['postid'], true); // call delete post function
    }
    else if(isset($_REQUEST['commentid'])){
        $comment_id = $_REQUEST['commentid'];

        if(getCommentById($comment_id)->user_id == getCurrentUser()->id || getPostById(getCommentById($comment_id)->post_id)->user_id == getCurrentUser()->id) {
            // Delete comment
            $statement = $pdo->prepare("DELETE FROM comment WHERE id = :pComment_Id");
            $statement->bindParam(':pComment_Id', $comment_id, PDO::PARAM_INT);
            
            if(!$statement->execute()){
                echo("Couldn't delete comment!"); // show error message
            }
        }
        else{
            die("<h1>You can't delete this comment because it isn't yours!</h1>"); // show warning message
        }
    }

    function deletePost(int $pPost_Id, bool $pRedirect){
        require("_db_connect.inc.php");

        // Delete all likes of this post
        $statementLikes = $pdo->prepare("DELETE FROM likes WHERE post_id = :pPost_Id");
        $statementLikes->bindParam(':pPost_Id', $pPost_Id, PDO::PARAM_INT);

        // Delete all comments of this post
        $statementComment = $pdo->prepare("DELETE FROM comment WHERE post_id = :pPost_Id");
        $statementComment->bindParam(':pPost_Id', $pPost_Id, PDO::PARAM_INT);

        if($statementLikes->execute() && $statementComment->execute() && getPostById($pPost_Id)->user_id == getCurrentUser()->id){
            // Delete post itself
            $statement = $pdo->prepare("DELETE FROM post WHERE id = :pId");
            $statement->bindParam(':pId', $pPost_Id, PDO::PARAM_INT);
            
            if(!$statement->execute()){
                echo("Couldn't delete post!"); // show error message
            }
            
            if($pRedirect){
                header("Location: /socialmedia/"); // redirect to index
                exit; // unsend header()
            }
        }
        else{
            die("<h1>You can't delete this post because it isn't yours!</h1>"); // show warning message
        }
    }
?>