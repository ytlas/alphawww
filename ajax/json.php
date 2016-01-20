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
if($_COOKIE['user_login']){
    list($cuser_name,$cookie_hash)=split(',',$_COOKIE['user_login']);
    if(crypt($cuser_name,$secret)==$cookie_hash){
	$un=$cuser_name;
    }
}
if(isset($_GET['request'])){
    $request=$_GET['request'];
    $con=con();
    date_default_timezone_set('Europe/Stockholm');
    if($con->connect_error){
	die('Connection failed: '.$con->connect_error);
	exit;
    }
    else{
	if(!$un&&$request==='user_login'&&isset($_GET['user_name'])&&isset($_GET['user_pass'])){
	    $user_name=$con->real_escape_string($_GET['user_name']);
	    $user_pass=$con->real_escape_string(acrypt($_GET['user_pass']));
	    $user_active=date('Y-m-d H:i:s');
	    $user_ip=$_SERVER['REMOTE_ADDR'];
	    $result=$con->query("SELECT * FROM user WHERE user_name='$user_name' AND user_pass='$user_pass' LIMIT 1");
	    if($result->num_rows>0){
		$row=$result->fetch_assoc();
		$id=$row['user_id'];
		setcookie('user_login',$user_name.','.crypt($user_name,$secret),time()+(86400*30),"/");
		echo '{"user_name":"'.$user_name.'"}';
	    }
	}
	elseif($un&&$request==='user_login'){
	    echo '{"user_name":"'.$un.'"}';
	}
	elseif($un&&$request==='user_logout'){
	    //echo $un."<br>".$_COOKIE['user_login'];
	    unset($_COOKIE['user_login']);
	    setcookie('user_login',null,-1,'/');
	    //echo $_COOKIE['user_login'];
	    echo "Attempted to log ".$un." out.";
	}
	elseif(!$un&&$request==='user_register'&&isset($_GET['user_name'])&&isset($_GET['user_pass'])){
	    $user_name=$con->real_escape_string($_GET['user_name']);
	    $user_pass=$con->real_escape_string(acrypt($_GET['user_pass']));
	    $user_registered=date('Y-m-d H:i:s');
	    $user_ip=$_SERVER['REMOTE_ADDR'];
	    if(strlen($user_name)>2&&preg_match("#^[a-zA-Z0-9\-\_\.]+$#",$user_name)&&mysqli_num_rows($con->query("SELECT * FROM user WHERE user_name='$user_name'"))==0){
		if($con->query("INSERT INTO user (user_name,user_pass,user_ip,user_rank,user_registered,user_active) VALUES ('$user_name','$user_pass','$user_ip',0,'$user_registered','$user_registered')")){
		    echo "{'user_name':".$user_name."}";
		}
	    }
	}
	else{
	    echo "No valid request?";
	}
    }
    $con->close();
}
?>
