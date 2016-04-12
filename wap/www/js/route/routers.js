/**
 * Created by chenchao on 13/8/15.
 */

(function () {

  var app = angular.module('DZ');
  var routeTable = {
    //index
    "guide": "/guide",

    //app
    "upload": '/upload',
    "do-upload-baseinfo": '/do-upload-baseinfo',
    "do-upload-lawyer": '/do-upload-lawyer',
    "do-upload-evidence": '/do-upload-evidence/:id',
    "login": '/login/:type',
    "setting": '/setting',
    "notice-list": '/notice-list',
    "details": '/details/:id',
    "register": '/register',
    "register-lawyer-complete": '/register-lawyer-complete',
    "lawyer-index": '/lawyer-index',
    "case-list-lawyer": '/case-list-lawyer',
    "lawyer-message": '/lawyer-message',
    "victim": '/victim',
    "law": '/law',
    "aboutus": '/aboutus',
    "usercenter": '/usercenter',
    "upload-success": '/upload-success',
    "list-by-lawyer": '/list-by-lawyer',
    "lawyer-image": '/lawyer-image',
  };

  app.config(['$stateProvider','$urlRouterProvider',function init($stateProvider, $urlRouterProvider) {

    //guide
    $stateProvider.state('index', {
      url: '/index',
      //abstract: true,
      templateUrl: 'templates/guide.html',
      controller: 'guideCtrl'

    });

    //404
    $stateProvider.state('page-404', {
      url: '/page-404',
      templateUrl: 'templates/error/page-404.html'
    });

    //创建 app
    $stateProvider.state('app', {
      url: '/app',
      abstract: true,
      templateUrl: 'templates/menu.html',
      controller: 'mainCtrl'
    });

    //创建 app.stateName
    for(var state in routeTable){
      if(routeTable.hasOwnProperty(state)){
        (function(state, route){
          $stateProvider.state('app.' + state, {
            url: route,
            views: {
              'menuContent': {
                templateUrl: 'templates' + route.split('/:')[0]+ '.html',
                //注意这里指定的对应 controller 需在 main 里加载
                //controller: (state == 'edit-upload' ? 'do-upload' : state) + 'Ctrl'
                controller:  state + 'Ctrl'
              }
            }
          })
        })(state, routeTable[state]);
      }
    }

    $urlRouterProvider.otherwise('/index');

  }]);

  app.run(['$ionicPlatform','$rootScope','$ls','$state','$unLogin',function ($ionicPlatform, $rootScope, $ls, $state, $unLogin) {
    var isToIndex = false;
    var name = '';
    var userInfo = $ls.getObject('userInfo');
    if(userInfo && userInfo.token){
      $rootScope.token = userInfo.token;
    }
    $rootScope.$on("$stateChangeStart", function(evt, to, toP, from, fromP) {
      name = to.name;
      isToIndex = to.name == 'index';
    });
//    $rootScope.$on("$locationChangeSuccess", function(evt, to, toP, from, fromP) {
//      $rootScope.isLogin = $ls.get('isLogin') == 1;
//      if($unLogin.indexOf(name) > -1){
//        return false;
//      }
//      if(!isToIndex && !$rootScope.isLogin){
//        var type = $ls.get('selectType') || '1';
//        $state.go('app.login', {type: type});
//      }
//    });
  }]);

}());