<?php
# php document only for use with GET requests and JSON responses, designed for ajax

# Simple Salt crypt function
function acrypt($str){
    return crypt($str,'ytlas');
}

# Local mysql connection to database
function con(){
    $con=new mysqli('localhost','adam','Qw!kmdo<','leafscript');
    return $con;
}

# Returns sql query result if name and password is correct, otherwise return NULL
function user_authByPass($user_name,$user_pass,$con){
    $result=$con->query("SELECT * FROM user WHERE user_name='$user_name' AND user_pass='$user_pass' LIMIT 1");
    if($result->num_rows>0){
        return $result;
    }
    else{
        return NULL;
    }
}

function user_name_validate($user_name){
    echo "fuck";
}

function user_pass_validate($user_pass){
    if(strlen($user_pass)>5&&strlen($user_pass)<31){
        return true;
    }
}

# "Global" variable declaration
# "Secret" cookie string to encrypt it later
$secret='solrun';

# Cookie username variable declaration, for use with conditions in requests
$un=NULL;

# Cookie validation
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
            if($result=user_authByPass($user_name,$user_pass,$con)){
                $row=$result->fetch_assoc();
                $user_rank=$row['user_rank'];
                $user_email=$row['user_email'];
                if($user_rank==0){
                    echo '{"user_rank":'.$user_rank.',';
                    echo '"user_name":"'.$user_name.'"}';
                }
                else{
                    setcookie('user_login',$user_name.','.crypt($user_name,$secret),time()+(86400*30),"/");
                    echo '{"user_name":"'.$user_name.'","user_email":"'.$user_email.'","user_rank":"'.$user_rank.'"}';
                }
            }
        }
        elseif($un&&$request==='user_login'){
            //Update last active and ip of user
            $user_ip=$_SERVER['REMOTE_ADDR'];
            $user_active=date('Y-m-d H:i:s');
            $con->query("UPDATE user SET user_active='$user_active', user_ip='$user_ip' WHERE user_name='$un'");
            $result=$con->query("SELECT * FROM user WHERE user_name='$un'");
            $row=$result->fetch_assoc();
            $user_email=$row['user_email'];
            echo '{"user_name":"'.$un.'","user_email":"'.$user_email.'"}';
        }
        elseif($un&&$request==='user_logout'){
            //echo $un."<br>".$_COOKIE['user_login'];
            unset($_COOKIE['user_login']);
            setcookie('user_login',null,-1,'/');
            //echo $_COOKIE['user_login'];
            echo "Attempted to log ".$un." out.";
        }
        elseif(!$un&&$request==='user_register'&&isset($_GET['user_name'])&&isset($_GET['user_pass'])&&isset($_GET['user_email'])){
            $user_name=$con->real_escape_string($_GET['user_name']);
            $user_pass=$con->real_escape_string(acrypt($_GET['user_pass']));
            $user_email=$con->real_escape_string($_GET['user_email']);
            $user_registered=date('Y-m-d H:i:s');
            $user_ip=$_SERVER['REMOTE_ADDR'];
            if(strlen($user_name)>2&&preg_match("#^[a-zA-Z0-9\-\_\.]+$#",$user_name)&&mysqli_num_rows($con->query("SELECT * FROM user WHERE user_name='$user_name' OR user_email='$user_email'"))==0&&strlen($_GET['user_pass'])>5&&strlen($_GET['user_pass'])<31){
                $user_activate_string=trim(acrypt(rand(1,100).$user_name),'.');
                if($con->query("INSERT INTO user (user_name,user_pass,user_email,user_ip,user_rank,user_registered,user_active,user_activate_string) VALUES ('$user_name','$user_pass','$user_email','$user_ip',0,'$user_registered','$user_registered','$user_activate_string')")){
                    $mailMessage='
                Hello '.$user_name.', click this link to activate your account.
                http://leafscript.net/ajax/json.php?request=user_activate&user_activate_string='.$user_activate_string.'
                ';
                    mail("$user_email","Account activation","$mailMessage");
                    echo '{"user_name":"'.$user_name.'","result":"success"}';
                }
            }
            else{
                echo '{"result":"failed"}';
            }
        }
        elseif(!$un&&$request==='user_activate'&&isset($_GET['user_activate_string'])){
            $user_activate_string=$con->real_escape_string($_GET['user_activate_string']);
            $result=$con->query("SELECT * FROM user WHERE user_activate_string='$user_activate_string'");
            if(mysqli_num_rows($result)>0){
                if($con->query("UPDATE user SET user_rank=1 WHERE user_activate_string='$user_activate_string'")){
                    echo "Successfully activated your accout! You may now log in @ <a href='http://leafscript.net/index.html'>home</a>.";
                }
            }
        }
        elseif($request=='user_changePass'&&isset($_GET['user_newPass'])){
            if($un&&isset($_GET['user_pass'])){
                if(user_authByPass($con->real_escape_string($un),$con->real_escape_string(acrypt($_GET['user_pass'])),$con)){
                    $user_name=$con->real_escape_string($un);
                    $user_pass=$con->real_escape_string(acrypt($_GET['user_newPass']));
                    echo "bitch";
                    if($con->query("UPDATE user SET user_pass='$user_pass' WHERE user_name='$user_name'")){
                        echo "fucker";
                    }
                }
            }
            else{
                echo "shit";
            }
        }
        else{
            echo "No valid request?";
        }
    }
    $con->close();
}
?>
