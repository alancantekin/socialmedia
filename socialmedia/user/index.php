<?php
    require("../_helpers/_init.inc.php");
?>

<div class="content">
    <!-- first section -->
    <h1>User informations</b></h1>
    <div class='flex marginbottom'>
        <div class='halfwidth marginright'>
            User id: <?php echo getCurrentUser()->id; ?><br>
            Username: <?php echo getCurrentUser()->username; ?><br>
            Email: <?php echo getCurrentUser()->email; ?>
        </div>
        <div class='halfwidth'>
            Created on <?php echo date('l, d.m.Y', strtotime(getCurrentUser()->date_created))." at ".date('H:i', strtotime(getCurrentUser()->date_created)) ?><br>
            Last modified on <?php echo date('l, d.m.Y', strtotime(getCurrentUser()->date_modified))." at ".date('H:i', strtotime(getCurrentUser()->date_modified)) ?>
        </div>
    </div>
    <!-- second section -->
    <h1>Edit my account</h1>
    <div class='marginbottom'>
        <!-- form to directly modify user data -->
        <!-- textboxes containg the current data -->
        <form method='POST' enctype='multipart/form-data'>
            Username<br>
            <input type='text' name='username' placeholder='username' value='<?php echo getCurrentUser()->username?>' maxlength="255" required/>
            Email<br>
            <input type='text' name='email' placeholder='email' value='<?php echo getCurrentUser()->email?>' maxlength="255" required/>
            Password<br>
            <input type='password' name='password' placeholder='password' value='<?php echo getCurrentUser()->password?>' maxlength="255" required/>
            Repeat Password<br>
            <input type='password' name='repeat_password' placeholder='repeat password' value='<?php echo getCurrentUser()->password?>' maxlength="255" required/>

            <?php
                if(isset($_POST['savebutton'])){ // save button clicked
                    if(!usernameAlreadyExists($_POST['username'], getCurrentUser()->username)){ // if username doesn't exist already
                        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // if email is valid
                            if($_POST['password'] == $_POST['repeat_password']){
                                $id = getCurrentUser()->id;
                                $username = $_POST['username'];
                                $email = $_POST['email'];
                                $password = $_POST['password'];
                                $date_modified = date("Y-m-d H:i:s");
                    
                                // prepare statement
                                $statement = $pdo->prepare(
                                    "UPDATE user 
                                    SET username = :pUsername, email = :pEmail, password = :pPassword, date_modified = :pDate_Modified 
                                    WHERE id = :pId"
                                );
                                $statement->bindParam(':pId', $id, PDO::PARAM_INT);
                                $statement->bindParam(':pUsername', $username, PDO::PARAM_STR);
                                $statement->bindParam(':pEmail', $email, PDO::PARAM_STR);
                                $statement->bindParam(':pPassword', $password, PDO::PARAM_STR);
                                $statement->bindParam(':pDate_Modified', $date_modified, PDO::PARAM_STR);
                                
                                if($statement->execute()){ // if statement executed successfully
                                    header("Refresh:0"); // reload page
                                    exit; // unsend header()
                                }
                                else{
                                    echo("Couldn'd register user!"); // message
                                }
                            }
                            else{
                                echo "Your password must correspond the repeated one!"; // message
                            }
                        }
                        else{
                            echo "\"".$_POST['email']."\" isn't a valid email address!"; // message
                        }
                    }
                    else{
                        echo "Username \"".$_POST['username']."\" is already taken! Please choose another one."; // message
                    }
                    echo "<br>";
                }
            ?>

            <button type='submit' name='savebutton'>Save Changes</button>
        </form>
    </div>
    
    <h1>My posts</h1>
    <!-- display posts of user -->
    <?php
        $user_id = getCurrentUser()->id;
        if(hasUserPostedByUserId($user_id)){
            echo "<div class='marginbottom wrapper animate' id='masonry'>";
            // get all posts of user out of database
            foreach ($pdo->query("SELECT * FROM post WHERE user_id = '$user_id' ORDER BY date_created DESC") as $row){
                $id = $row['id'];
                $user_id = $row['user_id'];
                $caption = $row['caption'];
                $image_name = $row['image_name'];
                $image = $row['image'];
                $date_created = $row['date_created'];
                $date_modified = $row['date_modified'];

                $pluralLikes = getLikesByPostId($id) != 1 ? "s" : ""; // plural version for likes
                $pluralComments = getCommentsByPostId($id) != 1 ? "s" : ""; // plural version for comments
                $commentClass = hasUserCommentedPost($id, getCurrentUser()->id) ? "s" : "r"; // fill/empty comment icon whether user has liked comment or not
                
                // display post with image, owner, caption, amount of likes/comments, date posted
                echo "
                    <div class='post'>
                        <div class='post-content'>
                            <div class='overlay'>
                                <img src='data:image;base64,".$image."' alt='$image_name'></img>
                                <div class='buttonoverlay'>
                                    <div class='icon'>
                                        <a href='../post/comment.php?id=$id'><i class='fa$commentClass fa-comment'></i></a> | 
                                        <a href='../post/?id=$id'><i class='fas fa-pencil-alt'></i></a> | 
                                        <a onclick='confirmDelete($id, \"post\")'><i class='fas fa-trash-alt'></i></a>
                                    </div>
                                </div>
                            </div>
                            <p id='like$id'>".getLikesByPostId($id)." Like$pluralLikes &#8729; ".getCommentsByPostId($id)." Comment$pluralComments</p>
                            <b>".getUserById($user_id)->username."</b> $caption
                            <div class='createdInfo'>posted on ".date('l, d.m.Y', strtotime($date_created))." at ".date('H:i', strtotime($date_created))."</div>
                        </div>
                    </div>
                ";
            }
            echo "</div>";
        }
        else { // if user hasn't posted anything yet
            echo "
            <div class='marginbottom'>
                You haven't posted anything yet.&nbsp;<a href='../post/'>Upload your first post</a>
            </div>";
        }
    ?>

    <!-- functionality to delete own account with confirmation if accidentally clicked --->
    <h1>Delete my account</h1>
    <div class='marginbottom'>
        <form method='POST' action='/socialmedia/_helpers/_delete.php' enctype='multipart/form-data'>
            <button type='submit' name='deleteuserbutton' class="deleteButton" onclick="return confirm('Are you sure you want to delete your account?')">Delete</button>
        </form>
    </div>
</div>

<?php
    require("../_helpers/_end.inc.php");
?>

<script>
    // arrange posts when page is loaded
    function waitForImages(){
        var allItems = document.getElementsByClassName('post');
        for(var i=0;i<allItems.length;i++){
            imagesLoaded( allItems[i], function(instance) {
            var item = instance.elements[0];
            resizeMasonryItem(item);
            } );
        }
    }

    function resizeMasonryItem(item){
        /* Get the grid object, its row-gap, and the size of its implicit rows */
        var grid = document.getElementById('masonry');
        var rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap'));
        var rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'));

        var rowSpan = Math.ceil((item.querySelector('.post-content').getBoundingClientRect().height+rowGap)/(rowHeight+rowGap));
        item.style.gridRowEnd = 'span ' + rowSpan;
    }

    function resizeAllMasonryItems(){
        // Get all item class objects in one list
        var allItems = document.getElementsByClassName('post');
        for(var i=0;i>allItems.length;i++){
            resizeMasonryItem(allItems[i]);
        }
    }
    
    /* Resize all the grid items on the load and resize events */
    var masonryEvents = ['load', 'resize'];
    masonryEvents.forEach( function(event) {
        window.addEventListener(event, resizeAllMasonryItems);
    } );
    /* Do a resize once more when all the images finish loading */
    waitForImages();
    // display content again when images are loaded so the process of aligning them can't be seen
    if(document.getElementById('masonry') != null){
        document.getElementById('masonry').style.display = "grid";
    }
</script>