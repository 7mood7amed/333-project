<?php ob_start(); ?>
<html>
<head>



<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0"/>

<title>eitiraaf | Register</title>
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<link rel="shortcut icon" href="images/circle-cropped.png" type="">
<link rel="stylesheet" href="css\navStyle.css">
<link rel="stylesheet" href="css\loginStyle.css" />
<link rel="stylesheet" href="css\profileStyle.css" />
<link rel="stylesheet" href="css\testNavSys.css" />
<link rel="stylesheet" href="css\fonts\Source Sans Pro.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
         integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
		  
<script defer src="https://use.fontawesome.com/releases/v5.0.13/js/all.js"
            integrity="sha384-xymdQtn1n3lH2wcu0qhcdaOpQwyoarkgLVxC/wZ5q7h9gHtxICrpcaSUfygqZGOe"
            crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" charset="utf-8"></script>
<script src="javascript_functions.js"></script>

<style>
body {
	
font-family: Source Sans Pro;
background: #fcf7ff;

}

.text-info {color: #ac68cc!important;}

        .logincont{
            width: 600px;
            margin: 0 auto;
            align-items: center;
        }
        .btn-outline-info{
            background-color: #ac68cc;
            border: none;
            color: white;
        }
        .btn-outline-info:hover{
            background-color: #ac68cf;
        }
        label{
            font-size: 14px;
            font-weight: bold;
        }


        @media (max-width: 991px) {
            .logincont{
                width: 100%;
            }
        }
		.forgot {
            text-decoration: none;
            color: grey;
        }
		.forgot:hover {
            color: grey;
            text-decoration: none;
        }
		
		* {
   box-sizing: border-box;
}
h1 {
   text-align: center;
}


button {
   margin: 10px auto 30px;
   display: table;
   font-size: 20px;
   padding: 10px 30px;
   background-color: #4caf50;
   border: none;
   color: #fff;
   border-radius: 4px;
   cursor: pointer;
}
button:hover {
   opacity: 0.8;
}
input[type="checkbox"] {
   height: 16px;
   width: 16px;
   margin-right: 5px;
   float: left;
}
.Social button.twitter-btn,
.Social button.facebook-btn {
   width: 70%;
   font-size: 18px;
  align:center;
}
.Social button.twitter-btn {
   background-color: #26abfd;
}
.Social button.facebook-btn {
   background-color: #3f68be;
}
.Social {
  
   padding-top: 30px;
   margin-top: 30px;
}
.Social button i {
   margin-right: 5px;
   font-size: 14px;
}
@media (max-width: 1000px) {
   form {
      width: 100%;
      margin-left: 0px;
   }
}
    </style>
	<script>
        function fieldValidation() {
            if (document.registerForm.name.value == "") {
               document.getElementById("fn_err").innerHTML = "Please enter your full name";
                return false;
            } else if (document.registerForm.username.value == "") {
               document.getElementById("un_err").innerHTML = "Please enter your username";
                return false;
            }else if (document.registerForm.email.value == "") {
				document.getElementById("e_err").innerHTML = "Please enter your email";
                return false;
            }else if (document.registerForm.pass.value == "") {
				document.getElementById("pw_err").innerHTML = "Please enter your password";
                return false;
            }
			else return true;
        }
		
		
		function closeRegisteration(){
			$("#registeration_modal").fadeOut();
		
			location.reload();
		}
		
	
	
    </script>
</head>
<?php
ob_start();
include('session.php');
include('functions.php');
require('connection.php');
?>

<div class="topnav" id="myTopnav">
	<img style='' src='images/Elogo.png' class='imGcenter'>	
</div>

<table  style="margin: 10px auto;"> 
    <tr><?php

        $emptyFields = false;
        $error = false;
        $success = false;
        $n="";
        $un="";
        $em="";
        $p="";
        
        extract($_POST);
        if(isset($register))
        {
            $n=$_POST['name'];
            $un=$_POST['username'];
            $em=$_POST['email'];
            $p=$_POST['pass'];
        }
        if(isset($register)){

			
				
            $result = $db->prepare("SELECT email FROM users WHERE email=?");
			$result->bindValue(1,$em);
            $result->execute();
            $count = $result->rowCount();

            if($count!=0)
            {
                echo "<script type ='text/javascript'>
            window.onload = function() {
            uniqueEmail();
            function uniqueEmail(){
                document.getElementById('e_err').innerHTML = 'An account with that email address already exists';
            };
        }
        </script>";
                $error = true;

            }
            if ((!(strlen($n) >= 3 && strlen($n) <= 20)) && !$error)
            {
			echo "<script type ='text/javascript'>
            window.onload = function() {
            nameLength();
            function nameLength(){
                document.getElementById('fn_err').innerHTML = 'Full name must have 3-20 characters';
				};
			}
			</script>";
				$error = true;
            }
			 if ((!(strlen($un) >= 3 && strlen($un) <= 16)) && !$error)
            {
			echo "<script type ='text/javascript'>
            window.onload = function() {
            usernameLength();
            function usernameLength(){
                document.getElementById('un_err').innerHTML = 'Username must have 3-16 characters';
				};
			}
			</script>";
				$error = true;
            }
			if(!preg_match('/^[^\s]+$/', $un)) {
				echo "<script type ='text/javascript'>
            window.onload = function() {
            whiteSpace();
            function whiteSpace(){
                document.getElementById('un_err').innerHTML = 'Username must not have spaces';
				};
			}
			</script>";
				$error = true;
			}

            $result = $db->prepare("SELECT username FROM users WHERE username=?");
			$result->bindValue(1,$un);
			$result->execute();
            $count = $result->rowCount();
            if($count!=0 && !$error)
            {
				echo "<script type ='text/javascript'>
            window.onload = function() {
            uniqueUsername();
            function uniqueUsername(){
                document.getElementById('un_err').innerHTML = 'An account with the same username is already registered';
				};
			}
			</script>";
                $error = true;
            }
			
			if(!isset($_POST["terms"]))
			{
				
				echo "<script type ='text/javascript'>
            window.onload = function() {
            termsAccepted();
            function termsAccepted(){
                document.getElementById('error').innerHTML = 'You must accept the terms and conditions to register';
				};
			}
			</script>";
				$error = true;
			}
           
            if (!$error && !(strlen($_POST['pass']) >= 6 && (strlen($_POST['pass'])) <= 16))
            {
          
				echo "<script type ='text/javascript'>
            window.onload = function() {
            passwordLength();
            function passwordLength(){
                document.getElementById('pw_err').innerHTML = 'Password length must be between 6 and 16 characters';
				};
			}
			</script>";
                $error = true;
            }
            else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && !$error)
            {
                 echo "<script type ='text/javascript'>
            window.onload = function() {
            invalidemail();
            function invalidemail(){
                document.getElementById('e_err').innerHTML = 'Invalid email address';
            };
        }
        </script>";
                $error = true;
            }
            	if(preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]+/u', $un)){
				echo "<script type ='text/javascript'>
            window.onload = function() {
            emoji();
            function emoji(){
                document.getElementById('un_err').innerHTML = 'Username must not have emojis';
				};
			}
			</script>";
				$error = true;
			}
           



            if (!$error)
            {
                $hash_password = md5($p);
                $sha1pass= sha1($hash_password);
                $query = "INSERT INTO users (name,username,email,password) VALUES
				(?,?,?,?)";
                $result = $db->prepare($query);
                $result->bindParam(1, $n);
                $result->bindParam(2, $un);
                $result->bindParam(3, $em);
                $result->bindParam(4, $sha1pass);
                $result->execute();

                if ($result->rowCount()==1)
                {
                    $success = true;
                    $_SESSION['active_user'] = $un;
                    $_SESSION['active_user_id'] = $db->lastInsertId();
                    $_SESSION['activeUserType'] = $type;
                    
					header("location:profile");
              
                }
                else
                {
                    error("Failed");
                }
            }
            $db=NULL;
        }
        ?>
    </tr>
    <body>






    </div>
    <div class="card" style="margin-top:64px;">
    
            <div style="padding-top: 16px;">
                <h2 class="text-info" style="text-align: center;padding-bottom: 5px;color: #ac68cc;">
			
				Register</h2>
                <div style="padding: 0 7% 7% 7%;">
                    <form action="" method="POST" name="registerForm" onsubmit="return fieldValidation()" >
                        <div class="form-group">
                            <label for="name" style="float: left;">Name</label>
                            <input type="text" class="form-control" id="name"   onkeyup="firstNameValidation()" name="name" <?php echo "value='$n'";?>/>
                            <span id="fn_err" style="color: red;"></span>
                        </div>
                        <div class="form-group">
                            <label for="username" style="float: left;">Username</label>
                                <input type="text" class="form-control" id="username"  onkeyup="usernameValidation(this.value);" name="username" <?php echo "value='$un'";?> />
                                <span id="un_err" style="color: red;"></span>

                        </div>
                        <div class="form-group">
                            <label for="email" style="float: left;">Email</label>
                            <input  onkeyup="emailValidation(this.value)" type="text" name="email" class="form-control" id="email" <?php echo "value='$em'";?> />
                            <span id="e_err" style="color: red;"></span>
                        </div>
                        <div class="form-group">
                            <label for="password" style="float: left;">Password</label>
                            <input  onkeyup="passwordValidation();" type="password" name="pass" class="form-control" id="password" <?php echo "value='$p'";?> />
                            <span id="pw_err" style="color: red;"></span>
                        </div>
						<label class="form-group" style="color: grey; font-weight: normal;padding-top: 16px;">
						
							<input type="checkbox" name="terms"
							<?php
							if (isset($_POST['terms']))
							{
								echo "checked";
							}
							?>>I am atleast 13 years old and have read the <a href="terms">terms and conditions</a> </label>
								<span id="error" style="color: red;"></span>
                        <p id="errorIDLogin"></p>
                        <div style="margin-top: 16px">
							
								 
                            <button type="submit" class="btn btn-outline-info" name="register" style="padding: 5px 45px;" onclick="">Register</button>
                        </div>
						  <div style="text-align: center; margin-top: 24px">
						   <div style="text-align: center;margin-top:4px;font-size: 12px;" ><a style="font-size: 15px;" href="login" class="forgot">Already registered?</a></div>
						 
                        </a>
                    </div>
                    </form>			 
						
            </div>
        </div>
    </div>
	

    </body>

    </html>