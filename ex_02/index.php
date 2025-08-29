<?php
session_start();
    // Si l'utilisateur n'est pas connecté, redirection vers login
    if(!isset($_SESSION["userName"])){
        header("Location: login.php");
        exit();
    }
 $userName = $_SESSION["userName"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>Hello <?php echo htmlspecialchars($userName);?></h1>
    
</body>
</html>