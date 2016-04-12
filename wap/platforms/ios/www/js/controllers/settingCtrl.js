/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('settingCtrl', ['$scope','$state','$api','$ls',function ($scope, $state, $api, $ls) {

    $scope.logout = function(){
      $ls.set('isLogin', 0);
      $ls.setObject('userInfo', null);
      $state.go('index');
    };

  }]);

}());