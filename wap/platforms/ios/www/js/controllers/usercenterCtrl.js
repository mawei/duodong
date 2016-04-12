/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('usercenterCtrl', ['$scope','$ls','$state','$rootScope','$api',function ($scope, $ls, $state,$rootScope,$api) {
	  		$scope.search = {
		      user_id: $rootScope.token
		    };
		    $scope.user = [];
		    $api.getUserInfo($scope.search, function(json){
		      if(json.message == 'success'){
		        $scope.user = json.data || [];
		      }
		    }, function(err){});
		    
			  $scope.role = $rootScope.role;

	  $scope.logout = function(){
	      $ls.set('isLogin', 0);
	      $ls.setObject('userInfo', null);
	      $state.go('index');
	      $rootScope.role = "";
	      $rootScope.lawyer_status = "";
	      $ro
	    };


  }]);

}());