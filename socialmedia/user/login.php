<?php
    require("../_helpers/_init.inc.php");
?>

<div class="content">
    <h1>Login</h1>
    <!-- login form -->
    <form method='POST' enctype='multipart/form-data'>
        <input type='text' name='username' placeholder="username" maxlength="255" required/>
        <input type='password' name='password' placeholder="password" maxlength="255" required/>
        <a href="register.php">Haven't got an account?</a><br>
        
        <?php
            if(isset($_POST['loginbutton'])){
                 // check if password corresponds the given user
                if($_POST['password'] == getUserByUsername($_POST['username'])->password){
                    // set session variable user_id to current logged in user id
                    $_SESSION['user_id'] = getUserByUsername($_POST['username'])->id;
                    header("Location: ../"); // redirect to index
                    exit; // unsend header()
                }
                else{
                    // display error message
                    echo "This isn't the correct password for \"".$_POST['username']."\"!";
                }
                echo "<br>";
            }
        ?>

        <button type='submit' name='loginbutton'>Login</button>
    </form>
</div>

<?php
    require("../_helpers/_end.inc.php");
?>