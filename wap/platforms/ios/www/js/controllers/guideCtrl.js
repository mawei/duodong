/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('guideCtrl', ['$scope','$rootScope','$state','$ionicModal','utilsService', function ($scope,$rootScope,$state,$ionicModal,utilsService) {

	  	  $scope.utilsService = utilsService;

		  $ionicModal.fromTemplateUrl('templates/login.html', {
			    scope: $scope,
			    animation: 'slide-in-up'
			  }).then(function(modal) {
				 $scope.modal = modal;
			  });
		  
		  	  $scope.openModal = function(role) {
		        $rootScope.role = role;
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
					    $scope.modal.remove();
				    // Execute action
				  });
			  
			  
  		}]);
  
  
}());

