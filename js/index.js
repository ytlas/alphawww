var app=angular.module('LSApp',[]);
app.controller('LSController',function($scope,$http,$interval){
    // "Global" variable initialization
    $scope.loggedIn=false;
    $scope.user_name;
    $scope.user_rank;
    // Display dictionary
    $scope.display={
	welcome:true,
	login:false,
	register:false,
	teamspeak:false
    }
    // Login attempt
    $http.get("ajax/json.php?request=user_login").success(function(response){
	if(response.user_name){
	    $scope.loggedIn=true;
	    $scope.user_name=response.user_name;
	}
    });

    // Functions
    $scope.onlyShow=function(onlyKey){
	for(var key in $scope.display){
	    if(onlyKey==key){
		$scope.display[key]=true;
	    }
	    else{
		$scope.display[key]=false;
	    }
	}
    }
    // Login function
    $scope.user_login=function(){
	$http.get("ajax/json.php?request=user_login&user_name="+$scope.user_name+"&user_pass="+$scope.user_pass).success(function(response){
	    $scope.user_name="";
	    $scope.user_pass="";
	    if(response.user_name&&response.user_rank>0){
		$scope.loggedIn=true;
		$scope.user_name=response.user_name;
		$scope.onlyShow('welcome');
	    }
	    else if(response.user_rank==0){
		alert("You have not activated your account yet. Check your e-mail.");
	    }
	    else{
		alert("Invalid username/password.");
	    }
	});
    }
    // Register function
    $scope.user_register=function(){
	$http.get("ajax/json.php?request=user_register&user_name="+$scope.user_name+"&user_pass="+$scope.user_pass+"&user_email="+$scope.user_email).success(function(response){
	    if(response.result=="failed"){
		$scope.user_pass="";
		alert("Failed. This might be because: ");
	    }
	    else if(response.result=="success"){
		alert("Successfully registered, you should now check the JUNK box of your e-mail to receive the activation link.");
		$scope.user_pass="";
		$scope.user_email="";
		$scope.onlyShow('welcome');
	    }
	});
    }

    // Log out function
    $scope.user_logout=function(){
	$http.get("ajax/json.php?request=user_logout").success(function(response){
	    $scope.loggedIn=false;
	});
    }
});
