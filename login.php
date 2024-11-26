<?php

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $registredEmail = "exp@stu.uob.edu.bh";   // to be replaced with email from database !! 
    $registredHashedPassword = password_hash("pass123" , PASSWORD_DEFAULT); // password to be replaced with real DB pass

    if($email !== $registredEmail){
        die("Invalid email or password.");
    }

    if(!password_verify($password,$registredHashedPassword)){
        die("Invalid email or password.");
    }

    echo "Login successful !";
}

?>