/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('upload-successCtrl', ['$scope','$state','$location','$ionicHistory',function ($scope,$state,$location,$ionicHistory) {

	  $ionicHistory.nextViewOptions({
		  disableAnimate: true,
		  disableBack: true
		});
	  
	  $scope.uploadConfirm = function(){
		  $state.go("app.upload");
	  }
	  
  }]);

}());