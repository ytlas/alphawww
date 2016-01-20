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
	    if(response.user_name){
		$scope.loggedIn=true;
		$scope.user_name=response.user_name;
		$scope.onlyShow('welcome');
	    }
	    else{
		$scope.loggedIn=false;
		alert("Invalid username/password");
	    }
	});
    }
    // Register function
    $scope.user_register=function(){
	$http.get("ajax/json.phṕ?request=user_register&user_name="+$scope.user_name+"&user_pass="+$scope.user_pass).success(function(response){
	    
	});
    }

    // Log out function
    $scope.user_logout=function(){
	$http.get("ajax/json.php?request=user_logout").success(function(response){
	    $scope.loggedIn=false;
	});
    }
});
