/**
 * Created by chenchao on 18/10/15.
 */

(function(){

	  return angular.module('DZ.controllers').controller('registerCtrl', ['$rootScope','$scope','$state','$api','$ls','$ionicPopup','$ionicPopover','$timeout','$ionicModal',function ($rootScope, $scope, $state, $api, $ls, $ionicPopup,$ionicPopover, $timeout,$ionicModal) {
	  	  
	    $scope.closeRegModal = function() {
		    $scope.reg_modal.hide();
		  };
		  //Cleanup the modal when we're done with it!
		  $scope.$on('$destroy', function() {
		    $scope.reg_modal.remove();
		  });
		  // Execute action on hide modal
		  $scope.$on('modal.hidden', function() {
		    // Execute action
		  });
		  // Execute action on remove modal
		  $scope.$on('modal.removed', function() {
			    $scope.reg_modal.remove();
		    // Execute action
		  });
	  
	    $scope.completeUserinfo = function(){
	        if(!$scope.data.password1 || !$scope.data.password2 || $scope.data.password1 != $scope.data.password2){
	          $ionicPopup.alert({
	            title: '提示信息',
	            template: '请确认密码是否一致！'
	          });
	          return false;
	        }
	        if(!$scope.data.nickname)
	        {
	        	$ionicPopup.alert({
		            title: '提示信息',
		            template: '请输入昵称'
		          });
		        return false;
	        }
	        $scope.data.username = $rootScope.username;
	        $scope.data.type = $rootScope.role;

	        $api.complete_userinfo($scope.data, function(json){
	          if(json.message == 'success'){
	            $ls.setObject('userInfo', {
	              type: $rootScope.role,
	              token: json.data
	            });
	            $rootScope.token = json.data;
	            if($rootScope.role == 'victim')
	            {
		            $state.go('app.upload');
	            }else{
	            	$state.go('app.do-upload-lawyer');
	            }
	        	  
	            $scope.closeRegModal();
	            $scope.closeModal();
	            //$scope.openModal();
	            $ionicPopup.alert({
		            title: '提示信息',
		            template: '注册成功'
		          });
	          }
	          if(json.message == 'failed'){
	            $ionicPopup.alert({
	              title: '提示信息',
	              template: json.data
	            });
	          }
	        });
	      };
	  
  }]);

}());