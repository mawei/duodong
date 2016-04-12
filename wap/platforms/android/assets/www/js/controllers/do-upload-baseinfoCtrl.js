/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('do-upload-baseinfoCtrl', ['$rootScope', '$scope', '$state', 'Upload','$timeout', '$ionicPopup', '$api', '$uploadPath',function ($rootScope, $scope, $state, Upload, $timeout, $ionicPopup, $api, $uploadPath) {

    $scope.cardFiles = [];
    $scope.cardViewFiles = [];
    $scope.protocolFiles = [];
    $scope.protocolViewFiles = [];
    $scope.otherFiles = [];
    $scope.otherViewFiles = [];
    $scope.data = {
      address: '',
      time: '',
      department: '',
      responsibility: '0',
      //evidence_id: '',
      user_id: $rootScope.token
    };
    $scope.case_id = $state.params.id;
    if($scope.case_id){
      $api.get_case_info({case_id: $scope.case_id}, function(json){
        if(json.message == 'success'){
          //$scope.info = json.data[0];
        }else{
          $ionicPopup.alert({
            title: '获取案例详情错误'
          });
        }
      });
    }

    $scope.submitUpload = function(){
      //$scope.data.evidence_id = getTotalEvidenceId();
      $api.create_case($scope.data, function(json){
        if(json.message == 'success'){
          $ionicPopup.alert({
            title: '已提交案件基本资料，请继续上传案件证据'
          }).then(function(res){
        	$rootScope.editingCase = json.data;
			$state.go('app.do-upload-evidence');
          });
        }else{
          $ionicPopup.alert({
            title: '提交案例资料失败!'
          });
        }
      });
    };

  }]);

}());