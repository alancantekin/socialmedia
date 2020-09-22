<?php
    require("_helpers/_init.inc.php");
?>
<?php
    if(hasAnyonePosted()){
        echo "
        <div id='loader'></div>
        <div class='content wrapper animate' id='masonry'>";
        // get all posts out of database
        foreach ($pdo->query("SELECT * FROM post ORDER BY date_created DESC") as $row){
            $id = $row['id'];
            $user_id = $row['user_id'];
            $caption = $row['caption'];
            $image_name = $row['image_name'];
            $image = $row['image'];
            $date_created = $row['date_created'];
            $date_modified = $row['date_modified'];

            $pluralLikes = getLikesByPostId($id) != 1 ? "s" : ""; // plural version for likes
            $pluralComments = getCommentsByPostId($id) != 1 ? "s" : ""; // plural version for comments
            $heartClass = hasUserLikedPost($id, getCurrentUser()->id) ? "s" : "r"; // fill/empty heart icon whether user has liked comment or not
            $commentClass = hasUserCommentedPost($id, getCurrentUser()->id) ? "s" : "r"; // fill/empty comment icon whether user has liked comment or not
            ?>
            <!-- display post with image, owner, caption, amount of likes/comments, date posted
            like, comment, edit, delete function -> depends on user rights -->
                <div class='post'>
                    <div class='post-content'>
                        <div class='overlay'>
                            <img src='data:image;base64,<?php echo $image?>' alt='<?php echo $image_name?>'></img>
                            <div class='buttonoverlay'>
                                <div class='icon'>
            <?php
            if($user_id == getCurrentUser()->id){ // provide comment, edit, delete function to owner of post
                echo "
                                    <a href='post/comment.php?id=$id'><i class='fa$commentClass fa-comment'></i></a> | 
                                    <a href='post/?id=$id'><i class='fas fa-pencil-alt'></i></a> | 
                                    <a onclick='confirmDelete($id, \"post\")'><i class='fas fa-trash-alt'></i></a>
                ";
            }
            else{ // provide like, comment function to any other user
                echo "
                                    <a onclick='likeFunction(".getCurrentUser()->id.", $id)' href='javascript:void(0);'><i id='heart$id' class='fa$heartClass fa-heart'></i></a> | 
                                    <a href='post/comment.php?id=$id'><i class='fa$commentClass fa-comment'></i></a>
                ";
            }
            ?>
            <!-- bottom part with owner, caption, amount of likes/comments, date posted -->
                                </div>
                            </div>
                        </div>
                        <p id='like<?php echo $id ?>'><?php echo getLikesByPostId($id)." like$pluralLikes &#8729; ".getCommentsByPostId($id)." comment$pluralComments"?></p>
                        <b><?php echo getUserById($user_id)->username ?></b> <?php echo $caption ?>
                        <div class='createdInfo'><?php echo "posted on ".date('l, d.m.Y', strtotime($date_created))." at ".date('H:i', strtotime($date_created)) ?></div>
                    </div>
                </div>
            <?php
        }
        echo "</div>";
    }
    else{ // display message if there aren't any posts
        echo "
        <div class='content'>
            <h1>No one has posted yet.</h1>
            <div style='flex-basis: 100%; height: 0;'></div>
            <a href='post/'>Be the first to post something</a>
        </div>
        ";
    }
?>

<?php
    require("_helpers/_end.inc.php");
?>

<script>
    // arrange posts when page is loaded
    function waitForImages(){
        var allItems = document.getElementsByClassName('post');
        for(var i = 0; i < allItems.length; i++){
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
        for(var i = 0; i > allItems.length; i++){
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
        document.getElementById("loader").style.display = "none";
        document.getElementById('masonry').style.display = "grid";
    }
</script>