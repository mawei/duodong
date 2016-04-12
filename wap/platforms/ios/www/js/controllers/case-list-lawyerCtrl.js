/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('case-list-lawyerCtrl', ['$rootScope', '$scope', '$api', function ($rootScope, $scope, $api) {
	  $scope.search = {
		      page: 1,
		      number: 10,
		      order: 'id desc',
		      status: '',
		      user_id: $rootScope.token
		    };
		    $scope.caseList = [];
		    $api.get_cases_by_lawyer($scope.search, function(json){
		      if(json.message == 'success'){
		        $scope.caseList = json.data || [];
		      }

		    }, function(err){});
  }]);

}());