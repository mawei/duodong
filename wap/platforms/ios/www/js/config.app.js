/**
 * Created by chenchao on 13/8/15.
 */

(function(){

  "use strict";

  // dev:开发环境 product:生产环境
  var env = 'product';
  //接口配置
  var apiConfig = {
    dev: '/server/',
    product: 'http://www.dazhuang001.com/index.php?/api/',
    ponydev: 'http://localhost/dazhuang/index.php?/api/',
    method: 'GET'
  };
  apiConfig.uploadPath = apiConfig[env] + 'upload_evidence';
  apiConfig.lawyerUploadPath = apiConfig[env] + 'upload_lawyer_image';
  apiConfig.domain = 'http://www.dazhuang001.com/';
  var apiList = function(conf){
    var apiMap = {};
    //接口列表
    var apiList = [
      'getUsers',
      'login',
      'get_authcode',
      'logout',
      'output_result',
      'get_lawyer_info',
      'get_messages',
      'create_case',
      'upload_lawyer_info',
      'get_cases',
      'get_case_info',
      'get_cases_by_victim',
      'get_cases_by_lawyer',
      'select_lawyer_to_case',
      'get_quotations',
      'create_payment',
      'get_payments',
      'upload_evidence',
      'register',
      'upload_user_photo',
      'complete_userinfo',
      'upload_lawyer_image',
      'getUserInfo',
      'get_lawyer_image',
      'get_lawyer_status',
    ];
    for(var i = 0; i < apiList.length; i++){
      var item = apiList[i];
      var name = '';
      var method = apiConfig['method'];
      if(typeof item != 'string'){
        name = item[0];
        method = item[1];
      }else{
        name = item;
      }
      if(!!name && !apiMap[name]){
        var api = apiMap[name] = {};
        api['name'] = name;
        api['path'] = conf[env] + name;
        api['method'] = method;
      }
    }
    return apiMap;
  };
  var config = angular.module('DZ.config');

  config.constant('$env', env);

  config.constant('$unLogin', [
      'index',
      'app.login',
      'app.register',
      'app.register-lawyer-step1',
      'app.register-lawyer-step2',
      'app.register-lawyer-step3',
      'app.register-lawyer-step4',
      'app.register-lawyer-step5',

      'app.case-list-lawyer',
      'app.lawyer-index',
      'app.lawyer-message',
  ]);

  config.constant('$ionicLoadingConfig', {
    //延迟出现的时间(ms)
    delay: 0,
    //模板
    template: '<i class="icon ion-loading-c"></i>\n<br/>\nLoading...',
    //是否显示遮罩
    noBackdrop: false
  });

  config.constant('$apiList', apiList(apiConfig));

  config.constant('$uploadPath', apiConfig.uploadPath);
  config.constant('$lawyerUploadPath', apiConfig.lawyerUploadPath);
  
  config.constant('$domain', apiConfig.domain);

  config.constant('$eventType', {
    event_0: '未选择',
    event_1: '主要责任',
    event_2: '次要责任',
    event_3: '全部',
    event_4: '无',
    event_5: '同等',
    event_6: '无法查明'
  });

}());