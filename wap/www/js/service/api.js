/**
 * Created by chenchao on 10/9/15.
 */

(function () {

  'use strict';

  var services = angular.module('DZ.services', ['ionic', 'DZ.config']);

  services.factory('$api', ['$http','$rootScope','$state','$ionicLoading','$ionicLoadingConfig','$env','$apiList',function($http, $rootScope, $state, $ionicLoading, $ionicLoadingConfig, $env, $apiList){
    var o = {};
    var createApi = function(query, apiInfo){
      var path = '';
      var apiName = apiInfo['name'];
      var method = apiInfo['method'];
      o[apiName] = function(params, success, error){
        var _params = params;
        var _success = success || function(){};
        if(typeof(_params) === 'function'){
          _success = _params;
        }

        if(method == 'GET'){
          path = apiInfo['path'];
          path = path + '?' + paramsToString(_params);
        }
        $ionicLoading.show();
        query({
          method: method,
          data: _params,
          url: path
        }).success(function(data){
          $ionicLoading.hide();
          _success && _success(data);
        }).error(function(err){
          $ionicLoading.hide();
          error && error(err);
        });
      }
    };
    for(var apiInfo in $apiList){
      if($apiList.hasOwnProperty(apiInfo)){
        createApi($http, $apiList[apiInfo]);
      }
    }
    return o;
  }]);

  function paramsToString(params){
    var buffer = [];
    for(var key in params){
      if(params.hasOwnProperty(key)){
        buffer.push(key + '=' + params[key]);
      }
    }
    return buffer.join('&');
  }

}());