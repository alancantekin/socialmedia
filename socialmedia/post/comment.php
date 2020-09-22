<?php
    require("../_helpers/_init.inc.php");
?>

<div class="content">
    <?php
        if(isset($_REQUEST['id']) && !empty($_REQUEST['id']) && getMaxPostId() >= $_REQUEST['id']){
            $post_id = $_REQUEST['id'];
        }
        else{
            die("<h1>This isn't a valid post id!</h1>"); // if invalid id is used as parameter
        }
    ?>
    <div class="flex" style="margin-bottom: 4.5em;">
        <!-- left part with image and infos -->
        <div class='commentImg'>
            <!-- img tag with image from database -->
            <img src='data:image;base64,<?php echo getPostById($post_id)->image ?>' alt='<?php echo getPostById($post_id)->image_name ?>'></img>
            <b><?php echo getUserById(getPostById($post_id)->user_id)->username ?></b> <?php echo getPostById($post_id)->caption ?>
             <div class='createdInfo'>posted on <?php echo date('l, d.m.Y', strtotime(getPostById($post_id)->date_created)) ?> at <?php echo date('H:i', strtotime(getPostById($post_id)->date_created)) ?></div>
            <?php
                // provide last modified information if user is owner of post
                if(getPostById($post_id)->user_id == getCurrentUser()->id){
                    echo "<div class='createdInfo'>you last modified on ".date('l, d.m.Y', strtotime(getPostById($post_id)->date_modified))." at ".date('H:i', strtotime(getPostById($post_id)->date_modified))."</div>";
                }
            ?>
        </div>
        <!-- comment section -->
        <div id='comments'>
            <?php
                if(getCommentsByPostId($post_id) >= 1){
                    foreach ($pdo->query("SELECT * FROM comment WHERE post_id = '$post_id' ORDER BY date_created") as $row){
                        $comment_id = $row['id'];
                        echo "
                        <div class='flex' id='commentDiv$comment_id'>
                            <div class='p'>
                                <b>".getUserById($row['user_id'])->username."</b> ".$row['text']."
                                <div class='createdInfo'>commented on ".date('l, d.m.Y', strtotime($row['date_created']))." at ".date('H:i', strtotime($row['date_created']))."</div>
                            </div>";
                            // give user possibility to delete comment if owner of post or of comment
                            if(getPostById($post_id)->user_id == getCurrentUser()->id || getCommentById($comment_id)->user_id == getCurrentUser()->id){
                                echo "<a onclick='confirmDelete($comment_id, \"comment\", $post_id)' class='deleteComment'><i class='fas fa-trash-alt'></i></a>";
                            }
                        echo "
                        </div>";
                    }
                }
                else{
                    echo "<p><i>No one has commented yet. Be the first!</i></p>"; // no one has commented message
                }
            ?>
        </div>
    </div>
    <iframe name="frame" style="display: none;"></iframe> <!-- prevents page from reloading -->
    <form enctype='multipart/form-data' target="frame" class='flex commentForm'> <!-- target="frame" so the page doesn't get refreshed when submitting form -->
        <input type='text' id='comment' class='commentInput' placeholder='Type in your comment here...' maxlength="500" required/> <!-- comment text box -->
        <button type="submit" onclick="commentFunction('<?php echo getCurrentUser()->id ?>', '<?php echo getCurrentUser()->username ?>', '<?php echo $post_id ?>')" href='javascript:void(0);' class='commentButton'><i class="fas fa-paper-plane"></i></button>
    </form>
</div>

<?php
    require("../_helpers/_end.inc.php");
?>