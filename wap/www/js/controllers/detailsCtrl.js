/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('detailsCtrl', ['$scope','$state','$api','$eventType',function ($scope, $state, $api, $eventType) {

    $scope.case_id = $state.params.id;
    $scope.disableEdit = true;
    $scope.info = {};


    $api.get_case_info({case_id: $scope.case_id}, function(json){
      if(json.message == 'success' && json.data && json.data[0]){
        $scope.info = json.data[0];
        $scope.disableEdit = $scope.info['status'] != '待审核';
        $scope.info.responsibility_CN = $eventType['event_' + $scope.info['responsibility']];
      }
    });

  }]);

}());