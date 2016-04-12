/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('mainCtrl', ['$rootScope','$ls','$state',function ($rootScope, $ls, $state) {

    $rootScope.toLogin = function(){
      var type = $ls.get('selectType') || '1';
      $state.go('app.login', {type: type});
    };

  }]);

}());