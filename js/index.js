var app=angular.module('LSApp',[]);
app.controller('LSController',function($scope,$http,$interval){
    // "Global" variable initialization
    $scope.loggedIn=false;
    // Display dictionary
    $scope.display={
	welcome:true,
	login:false,
	register:false
    }

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
});
