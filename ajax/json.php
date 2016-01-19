<?php
function acrypt($str){
    return crypt($str,'ytlas');
}
function con(){
    $con=new mysqli('localhost','adam','Qw!kmdo<','leafscript');
    return $con;
}
function getNameById($user_id){

}
$secret='solrun';
$un=NULL;
if($_COOKIE['login']){
    list($c_username,$cookie_hash)=split(',',$_COOKIE['login']);
    if(crypt($c_username,$secret)==$cookie_hash){
	$un=$c_username;
    }
}
if(isset($_GET['request'])){
    $request=$_GET['request'];
    $con=new mysqli("localhost","adam","Qw!kmdo<","leafscript");
    date_default_timezone_set('Europe/Stockholm');
    if($con->connect_error){
	die("Connection failed: ".$con->connect_error);
	exit;
    }
    else{
	if($request==="test"){
	    echo "Success";
	}
	if($request==="login" && !$un && isset($_GET['username']) && isset($_GET['password'])){
	    $username=$con->real_escape_string($_GET['username']);
	    $password=$con->real_escape_string(acrypt($_GET['password']));
	    $result=$con->query("SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1");
	    if(mysqli_num_rows($result)>0){
		$row=$result->fetch_assoc();
		$id=$row['id'];
		setcookie('login',$username.','.crypt($username,$secret),time()+(86400*30),"/");
		echo '{ "uid": "'.$username.'" }';
	    }
	}
	elseif($request==="logout" && $un){
	    echo $un."<br>".$_COOKIE['login'];
	    unset($_COOKIE['login']);
	    setcookie('login',null,-1,'/');
	    echo $_COOKIE['login'];
	}
	elseif($request==="users"){
	    $result=$con->query("SELECT * FROM user");
	    $outp="";
	    while($row=$result->fetch_array()){
		if($outp!=""){$outp.=",";}
		$outp.='{"username":"'.$row["user_name"].'",';
		$outp.='"date":"'.$row["user_registered"].'"}';
	    }
	    $outp='{"users":['.$outp.']}';
	    echo $outp;
	}
	$con->close();
    }
}
else{
}
?>
