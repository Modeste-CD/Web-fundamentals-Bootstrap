<?php
//define variables and set to empty value
$name_error = $email_error = $password_error = "";
$name = $email = $password = $password_confirmation = "";


// Vérifier sur le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Recuperation and validate from data 

    
    if (empty($_POST["name"])) {
        $name_error = "Name is required";         
    }else {
        $name = data_processing_input($_POST["name"]);
    }

    // check if name only contains letters and whitespace and greater than or equal to 3
    if (!preg_match("/^[a-zA-Z-' ]*$/",$name) || strlen($name)< 3) {
       $name_error = "Invalid name";
    }else {
        $name = data_processing_input($_POST["name"]);
    }

    if(empty($_POST["email"])) {
        $email_error = "Email is required";         
    }else {
    $email = data_processing_input($_POST["email"]);
    }

    // check if e-mail address is well-formed

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       $email_error = "Invalid Email";
    }else {
    $email = data_processing_input($_POST["email"]);
    }

    if (empty($_POST["password"]) || empty($_POST["password_confirmation"])) {
        $password_error = "Password is requiered";
    }elseif ($_POST["password_confirmation"] =! $_POST["password_confirmation"]) {
        $password_error = "Passwords do not match";
    }else {
        $password = data_processing_input($_POST["password"]);
    }
    
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $password_error = "Invalid password";
    }
    else {
        $password = data_processing_input($_POST["password"]);
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    }
}


// Function who process data input
function data_processing_input($data){
    $data = trim($data);
    $data = stripslashes($data); // stripslashes() : Remove backslashes \ from the user input data
    $data = htmlspecialchars($data);
    return $data;
}

// Insert into database

if (isset($name) && isset($email) && isset($passwordHash)) {
    $created_at = date("Y-m-d");
    $is_admin = 0;
    $host = "localhost";
    $username = "root";
    $passwd = "root";
    $port = 3306;
    $db = "gecko";

    try {
        $bdd = new PDO("mysql:host=$host;port=$port;dbname=$db", $username, $passwd); 
       // $bdd = new PDO('mysql:host=localhost;dbname=gecko', "root", "root"); 
        //set the PDO error mode to exception
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully";  
        $sql = "INSERT INTO users(name, password, email, created_at, is_admin)
        VALUES(:name, :password, :email, :created_at, :is_admin)";
        $my_Insert_Statement = $bdd->prepare($sql);
        $my_Insert_Statement->bindParam(":name", $name);
        $my_Insert_Statement->bindParam(":password", $passwordHash);
        $my_Insert_Statement->bindParam(":email", $email);
        $my_Insert_Statement->bindParam(":created_at", $created_at);
        $my_Insert_Statement->bindParam(":is_admin", $is_admin);
        $my_Insert_Statement->execute();
        $display_message = "User created";
        /*$sql = "INSERT INTO users(name, password, email, created_at)
        VALUES($name, $passwordHash, $email, $created_at)";*/       

    }
    catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
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
    <link rel="stylesheet" href="./CSS/style.css">
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
        <p class="display_message"><?php echo $display_message;?></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="mb-3">
                <label for="name" class="form-label">Your name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" value="<?php echo $name;?>">
                <span class="error">* <?php echo $name_error;?></span>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?php echo $email;?>">
                <span class="error">* <?php echo $email_error;?></span>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                <span class="error">* <?php echo $password_error;?></span>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Password confirmation</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" >
                <span class="error">* <?php echo $password_error;?></span>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
    </form>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</div>
</body>
</html>