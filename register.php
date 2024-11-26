<?php

// checking the request method
if($_SERVER['REQUEST_METHOD'] == "POST"){

    // getting inputs submitted from the form
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // validating UOB email
    $pattern = "/@stu\.uob\.edu\.bh/";
    if(!preg_match($pattern ,$email)){
        die("Invalid email address. Only UOB email (@uob.edu.bh) are allowed");
    }

    // hashing the password (( Must be stored in actual DATABASE !! ))
    $hashed_password = password_hash($password,PASSWORD_DEFAULT);

    // message after successful registration
    echo "Registereation successful for $name! head to login page ";

    // redirect to login page after successful registration
    header("Location: login.html");
    exit();
}

?>