/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('menuCtrl', ['$rootScope', '$scope', '$api', function ($rootScope, $scope, $api) {
	  $scope.role = $rootScope.role;
  }]);

}());