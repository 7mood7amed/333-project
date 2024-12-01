<?php
include("functions.php");
include("session.php");
require("connection.php");


?>
<html>
<head>


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0"/>

    <title>eitiraaf | Login</title>
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<link rel="shortcut icon" href="images/circle-cropped.png" type="">

    <link rel="stylesheet" href="css\navStyle.css">
    <link rel="stylesheet" href="css\loginStyle.css"/>
    <link rel="stylesheet" href="css\profileStyle.css"/>
    <link rel="stylesheet" href="css\testNavSys.css"/>
	<link rel="stylesheet" href="css\fonts\Source Sans Pro.css">
<script src="https://kit.fontawesome.com/12a7d4bf96.js" crossorigin="anonymous"></script>
    
    

		  	 
		
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>

    <script src="javascript_functions.js"></script>
    <script>
    
		
		function loginValidation(){
			if (document.loginForm.username.value == "") {
			document.getElementById("unerr").innerHTML = "Please enter your username";
			    return false;
			} else if (document.loginForm.password.value == "") {
			document.getElementById("pwerr").innerHTML = "Please enter your password";
				    return false;
			
			}
			else return true;
		}
		function notEmptyFields(){
			if (document.loginForm.username.value !== "") {
			document.getElementById("unerr").innerHTML = "";
			    return true;
			} else if (document.loginForm.password.value !== "") {
			document.getElementById("pwerr").innerHTML = "";
				return true;
			}
			else return false;
			
		}
		
	

    </script>

    <style>
        body {
            font-family: Source Sans Pro;
            /*padding: 20px;*/
            background: #fcf7ff;
        }

        .text-info {color: #ac68cc!important;}

        .logincont {
            width: 600px;
            margin: 0 auto;
            align-items: center;
            padding-top: 20px;
            height: 100vh;
        }

        .forgot {
            text-decoration: none;
            color: grey;
        }

        .forgot:hover {
            color: grey;
            text-decoration: none;
        }
        .btn-outline-info{
            background-color: #ac68cc;
            border: none;
            color: white;
        }
        .btn-outline-info:hover{
            background-color: #ac68cf;
        }


        @media (max-width: 991px) {
            .logincont {
                width: 100%;
           }
        }
		
		* {box-sizing: border-box}


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
   height: 14px;
   width: 14px;
   margin-right: 8px;
   float: left;
   margin-top: 2px;
}
.Social button.twitter-btn,
.Social button.facebook-btn {
   font-size: 16px;
   padding: 8px 24px;

}
.Social button.twitter-btn {
   background-color: #22a9f4;
}
.Social button.facebook-btn {
   background-color: #3f68be;
}
.Social {
  
   padding-top: 2px;
   margin-top: 2px;
}
.Social button i {
   margin-right: 2px;
   font-size: 14px;
}
@media (max-width: 1000px) {
   form {
      width: 100%;
      margin-left: 0px;
   }
}
    </style>
	
</head>
<div class="topnav" id="myTopnav">
	<img style='' src='images/Elogo.png' class='imGcenter'>
	</div>


<?php if (!isset($_SESSION['active_user']) && !isset($_SESSION['name']))
{

$error = false;
$success = false;
$un = "";
$pw = "";
extract($_POST);
	if (isset($login)) {
    $un = $_POST['username'];
    $pw = $_POST['password'];
    $hpw = md5($pw);
    $sha1pass = sha1($hpw);
	
		if(empty(trim($_POST['username'])) && empty(trim($_POST['password']))){
		echo "<div class='card' style='margin-top:55px;'>";
		error("Please enter your username and password");
		echo "</div>";
		$error = true;
		}
		elseif(empty(trim($_POST['username']))){
			echo "<div class='card' style='margin-top:55px;'>";
		error("Please enter your username");
		echo "</div>";
		$error = true;
		}
		elseif(empty(trim($_POST['password']))){
		echo "<div class='card' style='margin-top:55px;'>";
		error("Please enter your password");
		echo "</div>";
		$error = true;
		}
		if(!$error){

		$result = $db->prepare("SELECT id, username FROM users WHERE username=? && password = ?");
		$result->bindValue(1,$un);
		$result->bindValue(2,$sha1pass);
		$result->execute();
		$count = $result->rowCount();
		
		if ($count == 1) {
        
      
        $row = $result->fetch();
        $_SESSION['active_user'] = $row['username'];
        $_SESSION['active_user_id'] = $row['id'];
        $_SESSION['activeUserType'] = $row['type'];

        setcookie("active_user", $row['username'], time() + 31556926); // remember for 1 year
        setcookie("active_user_id", $row['id'], time() + 31556926);
        
		header("location:recieved");
		

		} else {
         echo "<script type ='text/javascript'>
            window.onload = function() {
            invalid();
            function invalid(){
                document.getElementById('invalid').innerHTML = 'Wrong username or password';
            };
        }
        </script>";
        $error = true;
       
    }
		}
}
?>

<div class="Card" style="margin-top:65px;">
				
            <div style="margin-top: 4%">
		
                <h2 class="text-info" style="text-align: center;padding-bottom: 2px;color: #ac68cc;font-size: 32px;margin-top: 0px;">
			
				Login</h2>
                <div style="padding: 7%;">
				
                      <form method="POST" action="" name="loginForm" id="login_form" onsubmit="return loginValidation();">
                        <div class="form-group">
								
                            <input  type="text" class="form-control form-group-lg" id="username" name='username' placeholder="Username" <?php echo"value='$un'"; ?> onkeyup="return notEmptyFields();" >
							<span id='unerr' style="color: red;"></span>
                        </div>
                        <div class="form-group">
                            <input   type="password" class="form-control" id="password" placeholder="Password" name="password" <?php echo "value='$pw'"; ?> onkeyup="return notEmptyFields()">
							<span id='pwerr' style="color: red;"></span>
                        </div>
						<span id='invalid' style="color: red; padding-bottom:10px;"></span>
                        <div class="form-row" style="padding: 0 8px;display: flex;">
                            <label class="form-group" style="color: grey; font-weight: normal;padding-top: 16px;">
                                <input type="checkbox" checked="checked" name="remember">Remember me</label>
                            <div class="form-group" style="margin-left: auto ">
                                <button type="submit" class="btn btn-outline-info form-group" id="login_form"  style="margin-left: auto; padding: 6px 45px" onclick='' name='login'>Login</button>
                            </div>
                        </div>
                    </form>
					
						<div class="Social"><a href="twitterapi"> 
<button type="submit" class="twitter-btn" style="margin: 4px auto 32px;">
 <i class="fa fa-twitter fa-xs"></i>Login with Twitter</button></a>
                
                    <div style="text-align: center;margin-top:4px;" ><a style="font-size: 15px;" href="forgotpassword" class="forgot">I Forgot My Password</a></div>
                    <div style="text-align: center; margin-top: 24px">
                    <div style="text-align: center;margin-top:4px;font-size: 12px;" ><a style="font-size: 15px;" href="register" class="forgot">Not registered?</a></div>
                
                    </div>
                </div>
            </div>
        </div>

    </div>
	  </div>
	  

	

    <?php
    }

    ?>
	 
    </body>

</html>