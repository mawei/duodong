/**
 * Created by chenchao on 13/8/15.
 */
(function(){

  return angular.module('DZ.controllers').controller('uploadCtrl', ['$scope','$rootScope','$ionicModal','$timeout','$api','$ionicSlideBoxDelegate','$state',function ($scope,$rootScope,$ionicModal,$timeout,$api,$ionicSlideBoxDelegate,$state) {
	  
	  
	  $scope.showList = false;
		  
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
	  	        $scope.caseList = json.data || [];
	  	      }
	  	    }, function(err){});
	  	    
	  	    
	      $ionicModal.fromTemplateUrl('templates/upload-info.html', {
		    scope: $scope,
		    animation: 'slide-in-up'
		  }).then(function(modal) {
		    $scope.modal = modal;
		  });
	  
	  	  $scope.openModal = function() {
		    $scope.modal.show();
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
		    // Execute action
		  });
		  
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