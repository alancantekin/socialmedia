<?php
    require("../_helpers/_init.inc.php");
?>

<div class="content">
    <h1>Register</h1>
    <!-- register form -->
    <form method='POST' enctype='multipart/form-data'>
        <input type='text' name='username' placeholder="username" maxlength="255" required/>
        <input type='text' name='email' placeholder="email" maxlength="255" required/>
        <input type='password' name='password' placeholder="password" maxlength="255" required/>
        <input type='password' name='repeat_password' placeholder="repeat password" maxlength="255" required/>
        <a href="login.php">Already have an account?</a><br>

        <?php
            if(isset($_POST['button'])){ // register button is clicked
                if(!usernameAlreadyExists($_POST['username'], "")){ // does username already exist
                    if(!strpos($_POST['username'], " ")){ // contains white spaces
                        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) { // is email valid
                            if($_POST['password'] == $_POST['repeat_password']){ // is password equals the repeated password
                                $username = $_POST['username'];
                                $email = $_POST['email'];
                                $password = $_POST['password'];
                                $date = date("Y-m-d H:i:s");
                    
                                // prepare statement
                                $statement = $pdo->prepare(
                                    "INSERT INTO user (username, email, password, date_created, date_modified) 
                                    VALUES (:pUsername, :pEmail, :pPassword, :pDate, :pDate)"
                                );
                                $statement->bindParam(':pUsername', $username, PDO::PARAM_STR);
                                $statement->bindParam(':pEmail', $email, PDO::PARAM_STR);
                                $statement->bindParam(':pPassword', $password, PDO::PARAM_STR);
                                $statement->bindParam(':pDate', $date, PDO::PARAM_STR);
                
                                if($statement->execute()){ // if satatement executed successfully
                                    header("Location: login.php"); // redirect to login page
                                    exit; // unsend header()
                                }
                                else{
                                    echo("Couldn'd register user!"); // error message
                                }
                            }
                            else{
                                echo "Your password must correspond the repeated one!"; // error message
                            }
                        }
                        else{
                            echo "\"".$_POST['email']."\" isn't a valid email address!"; // error message
                        }
                    }
                    else{
                        echo "You're not allowed to have white spaces in your username!";
                    }
                }
                else{
                    echo "Username \"".$_POST['username']."\" is already taken! Please choose another one."; // error message
                }
                echo "<br>";
            }
        ?>

        <button type='submit' name='button'>Register</button>
    </form>
</div>

<?php
    require("../_helpers/_end.inc.php");
?>