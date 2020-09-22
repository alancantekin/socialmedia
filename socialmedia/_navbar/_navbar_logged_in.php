<?php
    $url = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
    $url_var = explode('/', $url);
    $folder = end($url_var); // folder (directory) the current page is saved in
    if(isset($_POST['logoutbutton'])){
        session_destroy();
        header("Location: /socialmedia/user/login.php"); // redirect to login.php
    }
?>

<!--
    following is used to underline the page in the navigation bar the user is currently browsing on

    if ($folder == "socialmedia") 
    { 
        echo " active"; 
    }
 -->

<ul>
    <!-- aligned left -->
    <li>
        <!-- title and index link at the same time -->
        <a href='/socialmedia/' class='h1<?php if ($folder == "socialmedia") { echo " active"; } ?>'>Socialmedia</a>
    </li>
    <!-- aligned right -->
    <li class="right">
        <!-- logout button -->
        <form method='POST' enctype='multipart/form-data'>
            <input type="submit" name='logoutbutton' class="navButton" value="Logout" />
        </form>
    </li>
    <li class="right">
        <!-- post link -->
        <a href="/socialmedia/post/" <?php if ($folder == "post") { echo "class=\"active\""; } ?>><i class="fas fa-plus-circle"></i> / <i class='fas fa-comment navCommentIcon'></i>&nbsp;&nbsp;Post</a>
    </li>
    <li class="right">
        <!-- user profile link -->
        <a href="/socialmedia/user/" <?php if ($folder == "user") { echo "class=\"active\""; } ?>><i class="fas fa-user"></i>&nbsp;&nbsp;<?php echo getCurrentUser()->username ?></a>
    </li>
</ul>