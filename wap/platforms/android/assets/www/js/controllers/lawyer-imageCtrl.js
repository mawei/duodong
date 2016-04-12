/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('lawyer-imageCtrl', ['$scope','$ls','$state','$rootScope','$api','$domain',function ($scope, $ls, $state,$rootScope,$api,$domain) {
	  		$scope.search = {
		      user_id: $rootScope.token
		    };
		    $scope.user = [];
		    $api.get_lawyer_image($scope.search, function(json){
		      if(json.message == 'success'){
			    $scope.certificate_image = $domain + json.data[0]['certificate_image'];
			    $scope.entrust_image = $domain + json.data[0]['entrust_image'];
		      }

		    }, function(err){});
			  $scope.lawyer_status = $rootScope.lawyer_status;


			  $scope.logout = function(){
	      $ls.set('isLogin', 0);
	      $ls.setObject('userInfo', null);
	      $state.go('index');
	    };


  }]);

}());