

/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('register-lawyer-step4Ctrl', ['$rootScope', '$scope', '$state', 'Upload','$timeout', '$ionicPopup', '$api', '$uploadPath',function ($rootScope, $scope, $state, Upload, $timeout, $ionicPopup, $api, $uploadPath) {

    $scope.zhizhaoFiles = [];
    $scope.zhizhaoViewFiles = [];
    $scope.wenbenFiles = [];
    $scope.wenbenViewFiles = [];
    $scope.data = {
      case_id: $state.params.id
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

    $scope.uploadCardFiles = function(files){
      if(files){
        $scope.cardFiles = $scope.cardFiles.concat(files);
        angular.forEach($scope.cardFiles, function(file, i){
          var reader = new FileReader();
          reader.onload = function(e) {
            $scope.cardViewFiles[i] = e.target.result;
          };
          reader.readAsDataURL(file);
        });
        createUploadFn(files, $scope.case_id, 1);
      }
    };
    $scope.uploadProtocolFiles = function(files){
      if(files){
        $scope.protocolFiles = $scope.protocolFiles.concat(files);
        angular.forEach($scope.protocolFiles, function(file, i){
          var reader = new FileReader();
          reader.onload = function(e) {
            $scope.protocolViewFiles[i] = e.target.result;
          };
          reader.readAsDataURL(file);
        });
        createUploadFn(files, $scope.case_id, 2);
      }
    };
    $scope.uploadOtherFiles = function(files){
      if(files){
        $scope.otherFiles = $scope.otherFiles.concat(files);
        angular.forEach($scope.otherFiles, function(file, i){
          var reader = new FileReader();
          reader.onload = function(e) {
            $scope.otherViewFiles[i] = e.target.result;
          };
          reader.readAsDataURL(file);
        });
        createUploadFn(files, $scope.case_id, 3);
      }
    };

    $scope.delCardImg = function(file, index){
      delImg(file, function(){
        $scope.cardFiles.splice(index, 1);
        $scope.cardViewFiles.splice(index, 1);
      });
    };
    $scope.delProtocolImg = function(file, index){
      delImg(file, function(){
        $scope.protocolFiles.splice(index, 1);
        $scope.protocolViewFiles.splice(index, 1);
      });
    };
    $scope.delOtherImg = function(file, index){
      delImg(file, function(){
        $scope.otherFiles.splice(index, 1);
        $scope.otherViewFiles.splice(index, 1);
      });
    };

    function delImg(file, callback){
      $ionicPopup.confirm({
        title: '提示信息',
        template: '确认删除 ' + file.name + ' 吗？'
      }).then(function(res) {
        if(res){
          callback();
        }
      });
    }

    function createUploadFn(files, case_id, image_type){
      console.log('开始上传共' + files.length + '张！');
      doUpload(files);
      function doUpload(files){
        if(files.length === 0){
          console.log('队列中无文件，或已全部上传完毕！');
          return false;
        }
        var file = files[0];
        if (file && !file.$error) {
          console.log('开始上传文件：', file.name);
          file.upload = Upload.upload({
            //url: 'https://angular-file-upload-cors-srv.appspot.com/upload',
            url: $lawyerUploadPath,
            data: {user_id: $rootScope.token,image_type: image_type},
            file: file
          });
          file.upload.then(function (response) {
            $timeout(function () {
              file.result = response.data;
              files.shift();
              console.log(file.name, '上传成功！');
              console.log('还剩余' + files.length + '张！');
              doUpload(files);
            });
          }, function (response) {
            if (response.status > 0){
              console.log(file.name, '上传失败！');
              console.log('还剩余' + files.length + '张！');
              doUpload(files);
              /*$ionicPopup.alert({
                title: '上传错误',
                template: response.status + ': ' + response.data
              });*/
            }
          });
          file.upload.progress(function (evt) {
            file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
          });
        }
      }
    }

    $scope.submitUpload = function(){
     // $scope.data.evidence_id = getTotalEvidenceId();
        $state.go('app.list');

    };

  }]);

}());