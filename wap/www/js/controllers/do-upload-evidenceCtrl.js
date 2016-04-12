/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('do-upload-evidenceCtrl', ['$rootScope', '$scope', '$state', 'Upload','$timeout', '$ionicPopup', '$api', '$uploadPath',function ($rootScope, $scope, $state, Upload, $timeout, $ionicPopup, $api, $uploadPath) {
    var image_height = 60;
    $scope.cardFiles = [];
    $scope.cardViewFiles = [];
    $scope.protocolFiles = [];
    $scope.protocolViewFiles = [];
    $scope.otherFiles = [];
    $scope.otherViewFiles = [];
    
    $scope.height1 = image_height;
    $scope.height2 = image_height;
    $scope.height3 = image_height;
    $scope.data = {
      case_id: $state.params.id
    };
    var file1 = 0;
    var file2 = 0;
    var file3 = 0;
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
    	  file1 = file1 + files.length; 
      	$scope.height1 = Math.ceil(file1/5)*image_height;

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
    	file2 = file2 + files.length; 
    	$scope.height2 = Math.ceil(file2/5)*image_height;
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
    	  file3 = file3 + files.length; 
      	$scope.height3 = Math.ceil(file3/5)*image_height;

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
    	file1 = file1 - 1; 
        $scope.height1 = Math.ceil(file1/5)*image_height;

        $scope.cardFiles.splice(index, 1);
        $scope.cardViewFiles.splice(index, 1);
      });
    };
    $scope.delProtocolImg = function(file, index){
      delImg(file, function(){
    	  file2 = file2 - 1; 
          $scope.height2 = Math.ceil(file2/5)*image_height;
          
        $scope.protocolFiles.splice(index, 1);
        $scope.protocolViewFiles.splice(index, 1);
      });
    };
    $scope.delOtherImg = function(file, index){
      delImg(file, function(){
    	  file3 = file3 - 1; 
          $scope.height3 = Math.ceil(file3/5)*image_height;
          
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
            url: $uploadPath,
            
            data: {case_id: $state.params.id,image_type: image_type},
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
        $state.go('app.upload-success');

//      $api.create_case($scope.data, function(json){
//        if(json.message == 'success'){
//          $ionicPopup.alert({
//            title: '案件证据提交成功'
//          }).then(function(res){
//            $state.go('app.list');
//          });
//        }else{
//          $ionicPopup.alert({
//            title: '案件证据提交失败!'
//          });
//        }
//      });
    };

//    function getTotalEvidenceId(){
//      var temp = [];
//      angular.forEach($scope.cardFiles, function(file){
//        file.result && temp.push(file.result.data);
//      });
//      angular.forEach($scope.protocolFiles, function(file){
//        file.result && temp.push(file.result.data);
//      });
//      angular.forEach($scope.otherFiles, function(file){
//        file.result && temp.push(file.result.data);
//      });
//      return temp.join(',');
//    }

  }]);

}());