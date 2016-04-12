/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('notice-listCtrl', ['$rootScope','$scope','$api','$timeout',function ($rootScope, $scope, $api,$timeout) {
    $scope.search = {
      page: 1,
      number: 10,
      order: 'id desc',
      status: '',
      user_id: $rootScope.token
    };
    $scope.caseList = [];
    $api.get_cases_by_victim($scope.search, function(json){
      if(json.message == 'success'){
	    	  return $scope.caseList;
      }

    }, function(err){});
    
    $scope.doRefresh = function() {  
	    $timeout( function() {  
	    	$scope.search = {
	  	  	      page: 1,
	  	  	      number: 10,
	  	  	      order: 'id desc',
	  	  	      status: '',
	  	  	      user_id: $rootScope.token
	  	  	    };

	  	  	    $scope.caseList = [];
	  	  	    $api.get_cases_by_victim($scope.search, function(json){
	  	  	      if(json.message == 'success'){
	  	  	    	  return $scope.caseList;
//	  	  	        $scope.caseList = json.data || [];
	  	  	      }
	  	  	    }, function(err){});
	      
	    }, 1000)
	    .finally(function() {
	        // 停止广播ion-refresher
	        $scope.$broadcast('scroll.refreshComplete');
	      });
	        
	  }; 
	  
  }]);

}());