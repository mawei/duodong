/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('loginCtrl', ['$rootScope','$scope','$state','$api','$ls','$ionicPopup','$ionicPopover','$timeout','$ionicModal',function ($rootScope, $scope, $state, $api, $ls, $ionicPopup,$ionicPopover, $timeout,$ionicModal) {
    var type = $rootScope.role;
    
    $ls.set('selectType', type);

    $scope.data = {
      username: '',
      type: type,
      code: ''
    };
    var timeoutFn;
    var countDownSecond = 60;
    var isShowLogin = true;
    $scope.isSendAuthCode = false;
    $scope.countDownSecond = countDownSecond;
    $scope.isShowLogin = isShowLogin;
    
    $scope.loginButtonType = 'button button-positive button-block';
	$scope.registerButtonType = 'button button-light button-block';

    $scope.showLogin = function(){
    	$scope.isShowLogin = true;
    	$scope.loginButtonType = 'button button-positive button-block';
    	$scope.registerButtonType = 'button button-light button-block';
    };
    
    $scope.showRegister = function(){
    	$scope.isShowLogin = false;
    	$scope.loginButtonType = 'button button-light button-block';
    	$scope.registerButtonType = 'button button-positive button-block';
    };
    
      $scope.closeModal = function() {
	    $scope.modal.hide();
	  };
	  //Cleanup the modal when we're done with it!
	  $scope.$on('$destroy', function() {
	    $scope.modal.remove();
	  });
	  // Execute action on hide modal
	  $scope.$on('modal.hidden', function() {
	    // Execute action
	  });
	  // Execute action on remove modal
	  $scope.$on('modal.removed', function() {
		    $scope.modal.remove();
	    // Execute action
	  });
	  
	  $ionicModal.fromTemplateUrl('templates/register.html', {
		    scope: $scope,
		    animation: 'slide-in-up'
		  }).then(function(reg_modal) {
			 $scope.reg_modal = reg_modal;
		  });
	  
	  	  $scope.openRegisterModal = function() {
		    $scope.reg_modal.show();
		  };
		  
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
    
    $scope.doLogin = function(){

      if(!$scope.data.username || !$scope.data.password){
        $ionicPopup.alert({
          title: '提示信息',
          template: '请输入电话号码和密码！'
        });
        return false;
      }
      $scope.data.type = $rootScope.role;
      $api.login($scope.data, function(json){
        if(json.message == 'success'){
          $ls.set('isLogin', 1);
          
          $ls.setObject('userInfo', {
            type: type,
            token: json.data,
          });
          $rootScope.isLogin = true;
          if($rootScope.role == 'victim')
    	  {
          	  $state.go('app.upload');
    	  }
          else{
        	  $api.get_lawyer_status({user_id: json.data}, function(json2){
        		  if(json2.message == 'success')
        			  {
        			  	$rootScope.lawyer_status = json2.data;
        	          	$state.go('app.list-by-lawyer');
        			  }
        	  });
          }
          $scope.closeModal();
        }
        
        if(json.message == 'failed'){
        	
          $ionicPopup.alert({
            title: '提示信息',
            template: json.data
          });
        }
      });
    };
    
    $scope.doRegister = function(){
        if(!$scope.data.username || !$scope.data.code){
          $ionicPopup.alert({
            title: '提示信息',
            template: '请输入电话号码和验证码！'
          });
          return false;
        }
        $scope.data.type = $rootScope.role;
        $api.register($scope.data, function(json){
          if(json.message == 'success'){
            $ls.set('isLogin', 1);
            $ls.setObject('userInfo', {
              type: type,
              token: json.data
            });
            $rootScope.token = json.data;
            $rootScope.username = $scope.data.username;
            $scope.openRegisterModal();
            return false;
          }
          if(json.message == 'failed'){
            $ionicPopup.alert({
              title: '提示信息',
              template: json.data
            });
          }
        });
      };

    $scope.getCode = function(){
      if($scope.data.username) {
        $api.get_authcode({mobile: $scope.data.username}, function (json) {
          if(json.message == 'success'){
            $scope.isSendAuthCode = true;
            timeoutFn = cutDown();
            $ionicPopup.alert({
              title: '提示信息',
              template: '动态码已发送!'
            });
          }
        });
      }else{
        $ionicPopup.alert({
          title: '提示信息',
          template: '请填写手机号码!'
        });
      }
    };

    function cutDown() {
      return $timeout(function () {
        if ($scope.countDownSecond > 0) {
          $scope.countDownSecond -= 1;
          cutDown();
        } else {
          $scope.isSendAuthCode = false;
          $scope.countDownSecond = countDownSecond;
          $timeout.cancel(timeoutFn);
        }
      }, 1000);
    }

  }]);

}());