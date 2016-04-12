/**
 * Created by chenchao on 13/8/15.
 */
(function(){

  return angular.module('DZ.controllers').controller('list-by-lawyerCtrl', ['$scope','$rootScope','$ionicModal','$timeout','$api','$ionicSlideBoxDelegate',function ($scope,$rootScope,$ionicModal,$timeout,$api,$ionicSlideBoxDelegate) {
	  $scope.showList = false;
	  $scope.role = $rootScope.role;
	  $scope.lawyer_status = $rootScope.lawyer_status;
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
	  	    

		  //Cleanup the modal when we're done with it!
		  $scope.$on('$destroy', function() {
//		    $scope.modal.remove();
		  });
		  // Execute action on hide modal
		  $scope.$on('modal.hidden', function() {
		    // Execute action
		  });
		  // Execute action on remove modal
		  $scope.$on('modal.removed', function() {
		    // Execute action
		  });
		  
		  $scope.doRefresh = function() {  
			    $timeout( function() { 
			    	$api.get_lawyer_status({user_id: $rootScope.token}, function(json2){
		        		  if(json2.message == 'success')
		        			  {
		        			  	$rootScope.lawyer_status = json2.data;
		        			  	$scope.lawyer_status = json2.data;
		        			  }
		        	  });
			    	
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
			      
			    }, 1000)
			    .finally(function() {
			        // 停止广播ion-refresher
			        $scope.$broadcast('scroll.refreshComplete');
			      });;  
			        
			  }; 
		  
  }]);

}());