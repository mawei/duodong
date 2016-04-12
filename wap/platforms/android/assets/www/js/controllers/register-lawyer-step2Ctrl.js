/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('register-lawyer-step2Ctrl', ['$rootScope','$scope','$state','$api','$ls','$ionicPopup','$timeout',function ($rootScope, $scope, $state, $api, $ls, $ionicPopup, $timeout) {

    $scope.data = {
      username: '',
      type: "lawyer",
      code: ''
    };
    var timeoutFn;
    var countDownSecond = 60;
    $scope.isSendAuthCode = false;
    $scope.countDownSecond = countDownSecond;
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
    $scope.nextStep = function(){
      if(!$scope.data.username || !$scope.data.code){
        $ionicPopup.alert({
          title: '提示信息',
          template: '请出入电话号码或者验证码！'
        });
        return false;
      }
      $api.login($scope.data, function(json){
        if(json.message == 'success'){
          $ls.set('isLogin', 1);
          $ls.setObject('userInfo', {
            type: $scope.data.type,
            token: json.data
          });
          $rootScope.isLogin = true;
          $state.go('app.register-lawyer-step3');
        }
        if(json.message == 'failed'){
          $ionicPopup.alert({
            title: '提示信息',
            template: json.data
          });
        }
      });
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