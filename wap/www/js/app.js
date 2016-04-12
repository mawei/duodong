(function app() {

  var app = angular.module('DZ', ['ionic','ionic.service.core','ionic.service.push','ngCordova', 'ngFileUpload', 'DZ.config', 'DZ.services', 'DZ.controllers', 'DZ.util'], ['$httpProvider',function($httpProvider){

  }]);

  app.run(['$ionicPlatform',function ($ionicPlatform) {
    $ionicPlatform.ready(function () {
      if (window.cordova && window.cordova.plugins.Keyboard) {
        cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
        cordova.plugins.Keyboard.disableScroll(true);
      }
      if (window.StatusBar) {
        StatusBar.styleDefault();
      }
    });
  }]);
  
  ionic.Platform.fullScreen(false, false);
  
  var push = new Ionic.Push({
      "debug": true
    });
  push.register(function(token) {
      console.log("Device token:",token.token);
    });

  angular.module('DZ.util', []);
  angular.module('DZ.config', []);
  angular.module('DZ.controllers', []);
  
  app.config(['$ionicAppProvider', function($ionicAppProvider) {
	  // Identify app
	  $ionicAppProvider.identify({
	    // The App ID (from apps.ionic.io) for the server
	    app_id: 'db277d38',
	    // The public API key all services will use for this app
	    api_key: '4bbf6ec64ec1d266c40c69d327adfe1853f250da97ff5ef2',
	    // Set the app to use development pushes
	    dev_push: true
	  });
	}])
	
	app.config(['$ionicConfigProvider', function($ionicConfigProvider) {
		$ionicConfigProvider.views.maxCache(1);

		// note that you can also chain configs
		//		$ionicConfigProvider.backButton.text('Go Back').icon('ion-chevron-left');
	}]);
  
    app.service('utilsService', function($ionicModal) {

	    this.showModal = function() {

	        var service = this;

	        $ionicModal.fromTemplateUrl('templates/login.html', {
	          scope: null,
	          controller: 'loginCtrl'
	        }).then(function(modal) {
	            service.modal = modal;
	            service.modal.show();
	        });
	    };

	    this.hideModal = function() {
	        this.modal.hide();
	    };

	});

}());
