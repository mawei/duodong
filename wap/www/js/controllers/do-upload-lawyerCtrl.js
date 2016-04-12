/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('do-upload-lawyerCtrl', ['$rootScope', '$scope', '$state', 'Upload','$timeout', '$ionicPopup', '$api', '$lawyerUploadPath','$ionicSlideBoxDelegate',function ($rootScope, $scope, $state, Upload, $timeout, $ionicPopup, $api, $lawyerUploadPath,$ionicSlideBoxDelegate) {

	$scope.nextSlide = function() {
		 $ionicSlideBoxDelegate.next();
	}
	  
    $scope.uploadCertificateFile = function(file){
      if(file){
          var reader = new FileReader();
          reader.onload = function(e) {
            $scope.certificateViewFile = e.target.result;
          };
          reader.readAsDataURL(file);
        };
        createUploadFn(file, 1);
      };
      
    $scope.uploadEntrustFile = function(file){
      if(file){
          var reader = new FileReader();
          reader.onload = function(e) {
            $scope.entrustViewFile = e.target.result;
          };
          reader.readAsDataURL(file);
        };
        createUploadFn(file, 2);
    };

    $scope.delCertificateImg = function(file, index){
      delImg(file, function(){
        $scope.certificateFile = "";
        $scope.cardViewFiles.splice(index, 1);
      });
    };
    $scope.delEntrustImg = function(file, index){
      delImg(file, function(){
        $scope.protocolFiles.splice(index, 1);
        $scope.protocolViewFiles.splice(index, 1);
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
    
    function createUploadFn(file, image_type)
    {
    	  file.upload = Upload.upload({
            //url: 'https://angular-file-upload-cors-srv.appspot.com/upload',
            url: $lawyerUploadPath,
            
            data: {user_id: $rootScope.token,image_type: image_type},
            file: file
          });
          file.upload.then(function (response) {
            $timeout(function () {
              file.result = response.data;
            });
          }, function (response) {
            if (response.status > 0){
              $ionicPopup.alert({
                title: '上传错误',
                template: response.status + ': ' + response.data
              });
            }
          });
          file.upload.progress(function (evt) {
        	if(image_type == 1)
    		{
                $scope.progress1 = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
                if($scope.progress1 == 100)
            	{
                	$ionicSlideBoxDelegate.next();
            	}
    		}else{
                $scope.progress2 = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
    		}
            	
          });
    }

    $scope.submitUpload = function(){
     // $scope.data.evidence_id = getTotalEvidenceId();
        $state.go('app.register-lawyer-complete');

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