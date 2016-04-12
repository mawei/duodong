/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('register-lawyer-completeCtrl', ['$scope','$ionicHistory','$state',function ($scope,$ionicHistory,$state) {
	  $ionicHistory.nextViewOptions({
		  disableAnimate: true,
		  disableBack: true
		});
	  
	  $scope.uploadConfirm = function(){
		  $state.go("app.list-by-lawyer");
	  }
  }]);

}());