<?php
session_start();
/*if (isset($_SESSION["userName"]) && !empty($_SESSION["userName"])) {
    header("Location: index.php");
    exit();}*/

    //define variables and set to empty value
    $email_error = $password_error = "";
    $email = $password = "";

    // Function who process data input
    function data_processing_input($data){
        $data = trim($data);
        $data = stripslashes($data); // stripslashes() : Remove backslashes \ from the user input data
        $data = htmlspecialchars($data);
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        if(empty($_POST["Email"])) {
            $email_error = "Email is required";         
        }else {
        $email = data_processing_input($_POST["Email"]);
        }

        // check if e-mail address is well-formed

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid Email";
        }else {
        $email = data_processing_input($_POST["Email"]);
        }

        if (empty($_POST["Password"])) {
            $password_error = "Password is required";
        }else {
            $password = data_processing_input($_POST["Password"]);
            
        }
        
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            $password_error = "Invalid password";
        }
        else {
            $password = data_processing_input($_POST["Password"]);
            
        }


        $host = "localhost";
        $db = "gecko";
        $port = 3306;
        $username = "root";
        $passwd = "root";
        try {
            $bdd = new PDO("mysql:host=$host;dbname=$db;port=$port", $username, $passwd);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT name, password, email FROM users WHERE email = :email LIMIT 1";
            $statement = $bdd->prepare($sql);
            $statement->bindParam(":email", $email);
            $statement->execute();
            $user = $statement->fetch(PDO::FETCH_ASSOC);
            /*var_dump($user); Avec ses deux ligne j'ai verifier si $user contient vraiment les enregistrement dont j'ai fait leur select 
            die();*/   
            if ($user) {
                if (password_verify($password, $user["password"])) {
                    // Autentification réussir
                    $_SESSION["userName"] = $user["name"];
                    /*var_dump($_SESSION["userName"]);
                    die();*/

                    // redirection vers index.php

                    /*echo "djdjdjjd";
                    header("Refresh:5; url=index.php");*/
                    header("Location: index.php");
                    exit();
                }
            }

        } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        }  
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My_shop</title>
    <!-- CDN boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <!-- NPM boostrap  -->
    <link rel="stylesheet" href="./assets/bootstrap/dist/css/bootstrap.min.css"> 
    <style>
        .error{
            color : red;
        }
        .display_message{
            color : green;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- <p class="display_message"><php echo $display_message;?></p> -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <div class="mb-3">
                <label for="Email" class="form-label">Email address</label>
                <input type="text" class="form-control" id="Email" name="Email" placeholder="Enter your email" value="<?php echo $email;?>">
                <span class="error">* <?php echo $email_error;?></span>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="Password" placeholder="Enter your password">
                <span class="error">* <?php echo $password_error;?></span>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
    </form>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</div>
</body>
</html>