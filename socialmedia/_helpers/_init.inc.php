<!DOCTYPE html>
    <html lang="en">
        <head>
            <link rel="stylesheet" type="text/css" href="/socialmedia/content/style.css" /> <!-- personal stylesheet -->
            <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css" />
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>



            <!-- Worth it ????? -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>



            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
            <script src="/socialmedia/js/script.js"></script> <!-- personal javascript -->
            <title>socialmedia</title>
        </head>
        <body>
            <?php
                session_start();
                require("_functions.php"); // require functions
                require("_db_connect.inc.php"); // require db connection
                $_anonymousAccessAllowed = array("register.php", "login.php");
                $_isInArrray = in_array(pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME).".php", $_anonymousAccessAllowed); // check if anonymous (not logged in) access is allowed

                if (isset($_SESSION['user_id']) && !$_isInArrray) { // user is logged in and anonymous access isn't allowed on the current page
                    require($_SERVER["DOCUMENT_ROOT"]."/socialmedia/_navbar/_navbar_logged_in.php"); // require logged in navigation bar
                }
                else if(!isset($_SESSION['user_id']) && !$_isInArrray){ // user isn't logged in and anonymous access isn't allowed on the current page
                    require($_SERVER["DOCUMENT_ROOT"]."/socialmedia/_navbar/_navbar_logged_out.php"); // require logged out navigation bar
                    header("Location: /socialmedia/user/login.php"); // redirect to login.php
                    exit;
                }
                else if(isset($_SESSION['user_id']) && $_isInArrray){ // user is logged in and anonymous access is allowed on the current page
                    header("Location: /socialmedia/"); // redirect to index page (user has to log out before using login or register)
                    exit; // unsend header()
                }
                else {
                    require($_SERVER["DOCUMENT_ROOT"]."/socialmedia/_navbar/_navbar_logged_out.php"); // require logged out navigation bar
                }
            ?>