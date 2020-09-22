<?php
    require("../_helpers/_init.inc.php");
?>

<div class="content">
    <?php
        if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
            $id = $_REQUEST['id'];
            if(getPostById($id)->user_id == getCurrentUser()->id){ // post only editable of owner
                $header = "Editing Post";
                $caption = getPostById($id)->caption;
            }
            else{
                die("<h1>You can't edit this post because it isn't yours!</h1>");
            }
        }
        else{ // if id not set as parameter recognize as creation of new post
            $header = "New Post";
            $caption = "";
        }
    ?>
    <h1><?php echo $header ?></h1>
    <form method='POST' enctype='multipart/form-data'>
        <?php
            if(isset($_REQUEST['id'])){ // possibility for user to switch image of post
                echo "<input type='hidden' name='id' value='$id' />
                <input type='checkbox' id='replaceimage' name='replaceimage' onclick='changeFileInputStatus()' /> I want to replace the image of my post
                <input type='file' id='image' name='image' accept='image/*' disabled/>";
            }
            else{
                echo "<input type='file' id='image' name='image' accept='image/*' required/>";
            }
        ?>
        <!-- caption textbox -->
        <input type='text' name='caption' placeholder='caption' value='<?php echo $caption ?>' maxlength="500" required/>
        
        <?php
            if(isset($_POST['postbutton'])){ // post button is clicked
                $user_id = getCurrentUser()->id;
                $caption = $_POST['caption'];
                $image_name = addslashes($_FILES['image']['name']);
                $image = base64_encode(file_get_contents(addslashes($_FILES['image']['tmp_name'])));
                $date = date("Y-m-d H:i:s");
                
                // prepare statement
                $statement = $pdo->prepare("INSERT INTO post (user_id, caption, image_name, image, date_created, date_modified) VALUES (:pUser_Id, :pCaption, :pImage_Name, :pImage, :pDate, :pDate)");
                $statement->bindParam(':pUser_Id', $user_id, PDO::PARAM_INT);
                $statement->bindParam(':pCaption', $caption, PDO::PARAM_STR);
                $statement->bindParam(':pImage_Name', $image_name, PDO::PARAM_STR);
                $statement->bindParam(':pImage', $image);
                $statement->bindParam(':pDate', $date, PDO::PARAM_STR);

                if($statement->execute()){ // if satatement executed successfully
                    header("Location: ../"); // redirect to index
                    exit; // unsend header()
                }
                else{
                    echo "Couldn't upload your post!";
                }
                echo "<br>";
            }
            else if(isset($_POST['savebutton'])){ // save button is clicked
                $id = $_POST['id'];
                $caption = str_replace("'", "&#39;", $_POST['caption']);
                $image_name = addslashes($_FILES['image']['name']);
                $image = base64_encode(file_get_contents(addslashes($_FILES['image']['tmp_name'])));
                $date_modified = date("Y-m-d H:i:s");
                
                // prepare statement
                if(isset($_POST['replaceimage'])){ // if user wants to replace image as well
                    $statement = $pdo->prepare("UPDATE post SET caption = :pCaption, image_name = :pImage_Name, image = :pImage, date_modified = :pDate_Modified WHERE id = :pId");
                    $statement->bindParam(':pImage_Name', $image_name, PDO::PARAM_STR);
                    $statement->bindParam(':pImage', $image);
                }
                else{
                    $statement = $pdo->prepare("UPDATE post SET caption = :pCaption, date_modified = :pDate_Modified WHERE id = :pId");
                }
                $statement->bindParam(':pCaption', $caption, PDO::PARAM_STR);
                $statement->bindParam(':pDate_Modified', $date_modified, PDO::PARAM_STR);
                $statement->bindParam(':pId', $id, PDO::PARAM_INT);
                
                if($statement->execute()){ // if satatement executed successfully
                    header("Location: ../"); // redirect to index
                    exit; // unsend header()
                }
                else{
                    echo("Couldn'd modify post!");
                }
                echo "<br>";
            }
        ?>
        <?php
            // recognize if page is used for creating or editing
            // usage of appropriate button
            if(!isset($_REQUEST['id'])){
                echo "<button type='submit' id='submitbutton' name='postbutton'>Post</button>";
            }
            else{
                echo "<button type='submit' id='submitbutton' name='savebutton'>Save Changes</button>";
            }
        ?>
        <button type='button' onClick="window.location='/socialmedia/'">Discard</button>
    </form>
</div>

<?php
    require("../_helpers/_end.inc.php");
?>

<script>
    $('#image').change(function(){
        var ext = $('#image').val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            console.log('invalid extension!');
            document.getElementById("submitbutton").disabled = true;
        }
    });
</script>