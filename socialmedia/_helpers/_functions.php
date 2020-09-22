<!-- require("_db_connect.inc.php"); = db connection in every function required to have access on PDO variable -->

<?php
    require("_classes.php"); // require classes defined in _classes.php
    
    function getCurrentUser(){ // get currently logged in user as instance of UserModel with all information needed
        require("_db_connect.inc.php");
        $current_user = new UserModel(); // create instance of class UserModel
        $current_user->id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";
        if(isset($_SESSION['user_id'])){
            // get first user with this username (obviously only going to have one)
            foreach ($pdo->query("SELECT * FROM user WHERE id = '$current_user->id' LIMIT 1") as $row){
                $current_user->username = $row['username'];
                $current_user->password = $row['password'];
                $current_user->email = $row['email'];
                $current_user->date_created = $row['date_created'];
                $current_user->date_modified = $row['date_modified'];
            }
        }
        else{
            // assign attributes string empty if no user is logged in
            $current_user->username = "";
            $current_user->password = "";
            $current_user->email = "";
            $current_user->date_created = "";
            $current_user->date_modified = "";
        }
        return $current_user; // return user
    }

    function getUserByUsername(string $pUsername){ // search in user table where username is equals the parameter
        require("_db_connect.inc.php");
        $user = new UserModel();
        foreach ($pdo->query("SELECT * FROM user WHERE username = '$pUsername'") as $row){
            $user->id = $row['id'];
            $user->username = $pUsername; // obviously could also use $row['username']; here
            $user->password = $row['password'];
            $user->email = $row['email'];
            $user->date_created = $row['date_created'];
            $user->date_modified = $row['date_modified'];
        }
        return $user; // return user as instance of UserModel class
    }
    
    function getUserById(int $pUserId){ // search in user table where id is equals the parameter
        require("_db_connect.inc.php");
        $user = new UserModel();
        foreach ($pdo->query("SELECT * FROM user WHERE id = '$pUserId'") as $row){
            $user->id = $pUserId; // obviously could also use $row['id']; here
            $user->username = $row['username'];
            $user->password = $row['password'];
            $user->email = $row['email'];
            $user->date_created = $row['date_created'];
            $user->date_modified = $row['date_modified'];
        }
        return $user; // return user as instance of UserModel class
    }
    
    // used for new users or users who are changing their name
    function usernameAlreadyExists(string $pUsername, string $pCurrentUsername){ // function to check if username already exists
        if($pUsername == $pCurrentUsername){
            return false;
        }
        else{
            require("_db_connect.inc.php");
            $stmt = $pdo->query("SELECT COUNT(*) FROM user WHERE username = '$pUsername'"); // get amount of columns in table using this name
            return (bool) $stmt->fetchColumn(); // check if there are any records using this username
        }
    }

    function hasUserPostedByUserId(int $pUserId){ // function to check if user has already posted something using his user id
        require("_db_connect.inc.php");
        $stmt = $pdo->query("SELECT COUNT(*) FROM post WHERE user_id = '$pUserId'");
        return (bool) $stmt->fetchColumn();
    }

    function hasAnyonePosted(){ // function to check if there are records in the post table
        require("_db_connect.inc.php");
        $count = $pdo->query("SELECT * FROM post");
        return $count->rowCount() > 0; // check if amount of rows is higher than 0
    }                                   // if true: someone has posted || if false: someone has posted yet

    function getPostById(int $pPostId){ // search in post table where id is equals the parameter
        require("_db_connect.inc.php");
        $post = new PostModel();
        foreach ($pdo->query("SELECT * FROM post WHERE id = '$pPostId'") as $row){
            $post->id = $row['id'];
            $post->user_id = $row['user_id'];
            $post->caption = $row['caption'];
            $post->image_name = $row['image_name'];
            $post->image = $row['image'];
            $post->date_created = $row['date_created'];
            $post->date_modified = $row['date_modified'];
        }
        return $post; // return post as instance of PostModel class
    }

    function getLikesByPostId(int $pPostId){ // search in likes table where post_id is equals the parameter
        require("_db_connect.inc.php");
        $count = $pdo->query("SELECT * FROM likes WHERE post_id = '$pPostId'");
        return $count->rowCount(); // return likes of as a number
    }
    
    function hasUserLikedPost(int $pPostId, int $pUserId){ // check likes table if there are any records with
        require("_db_connect.inc.php");                    // these two parameters as post_id and user_id
        $count = $pdo->query("SELECT * FROM likes WHERE post_id = '$pPostId' AND user_id = '$pUserId'");
        return $count->rowCount() == 1; // get amount of records and check whether the amount is 1 or 0
    }

    function getCommentsByPostId(int $pPostId){ // search in comment table where post_id is equals the parameter
        require("_db_connect.inc.php");
        $count = $pdo->query("SELECT * FROM comment WHERE post_id = '$pPostId'");
        return $count->rowCount(); // return comments of as a number
    }
    
    function hasUserCommentedPost(int $pPostId, int $pUserId){ // check comment table if there are any records with
        require("_db_connect.inc.php");                        // these two parameters as post_id and user_id
        $count = $pdo->query("SELECT * FROM comment WHERE post_id = '$pPostId' AND user_id = '$pUserId'");
        return $count->rowCount() >= 1; // get amount of records and check whether the amount is bigger than 1 or not
    }

    function getCommentById(int $pCommentId){ // search in comment table where id is equals the parameter
        require("_db_connect.inc.php");
        $comment = new CommentModel();
        foreach ($pdo->query("SELECT * FROM comment WHERE id = '$pCommentId'") as $row){
            $comment->id = $row['id'];
            $comment->user_id = $row['user_id'];
            $comment->post_id = $row['post_id'];
            $comment->text = $row['text'];
            $comment->date_created = $row['date_created'];
        }
        return $comment; // return comment as instance of CommentModel class
    }

    // used for commenting with javascript and AJAX
    function getMaxPostId(){ // get id of latest uploaded post from post table
        require("_db_connect.inc.php");
        $maxId;
        foreach ($pdo->query("SELECT MAX(id) FROM post") as $row){
            $maxId = $row[0];
        }
        return $maxId;
    }
?>