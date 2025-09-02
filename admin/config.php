<?php

 $host = "localhost";
//  $host = "sql111.infinityfree.com";
 

 if($_SERVER['SERVER_NAME'] === 'localhost'){
   $username = "root";
   $password = "";
   $db_name = "hnb-council";
 }else{
   $username = "if0_39039702";
   $password = "LquKEp9MxeMSlv";
   $db_name = "if0_39039702_gehuevent";
 }
 
 
 $conn = mysqli_connect($host,$username,$password,$db_name);

 if(!$conn){
    echo "Something went wrong"+$conn;
 }



 function redirect($loc){
?>
<script>
   window.location.href='<?php echo $loc ?>';
</script>
<?php
}
?>