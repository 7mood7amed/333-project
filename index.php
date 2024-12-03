<?php

require('header.php');

if(isset($_SESSION['role']) == 'admin'){
//redirect to the admin page
header('location:adminhome.php');
} else{
    //normal users page
}




require('footer.php');
if(isset($_POST['comment'])){
    $query = "INSERT INTO comments (userid, comment, roomId, reply) values (?,?)";
}
?>
UPDATE comments SET reply= "somereply" WHERE ID= 

<form method="POST">
    <textarea name="comment" id="">
</textarea>
<button type="submit">submit</button>
</form>