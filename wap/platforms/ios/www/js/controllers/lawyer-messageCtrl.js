/**
 * Created by chenchao on 18/10/15.
 */

(function(){

  return angular.module('DZ.controllers').controller('lawyer-messageCtrl', ['$scope',function ($scope) {
    $scope.messageList = [
      {title: '您的案件已通过审核', time: '2015-10-21'},
      {title: '受害人已选择您的方案', time: '2015-05-21'},
      {title: '受害人已选择您的方案', time: '2015-10-09'},
    ];
  }]);

}());