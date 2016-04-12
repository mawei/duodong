<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		define("TOKEN", "weixin");
		$wechatObj = new wechatCallbackapiTest();
		if (!isset($_GET['echostr'])) {
			$wechatObj->responseMsg();
		}else{
			$wechatObj->valid();
		}
	}
}

class wechatCallbackapiTest
{
	public function valid()
	{
		$echoStr = isset($_GET["echostr"]) ? $_GET["echostr"] : "";

		//valid signature , option
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
	}

	public function responseMsg()
	{
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

		//extract post data
		if (!empty($postStr)){
			/* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
			 the best way is to check the validity of xml by yourself */
			libxml_disable_entity_loader(true);
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = $postObj->FromUserName;
			$toUsername = $postObj->ToUserName;
			$keyword = trim($postObj->Content);
			$time = time();
			$xmlTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[transfer_customer_service]]></MsgType>
						</xml>";
			$result = sprintf($xmlTpl, $fromUsername, $toUsername, time());
			echo $result;
				
			
// 			$textTpl = "<xml>
// 							<ToUserName><![CDATA[%s]]></ToUserName>
// 							<FromUserName><![CDATA[%s]]></FromUserName>
// 							<CreateTime>%s</CreateTime>
// 							<MsgType><![CDATA[%s]]></MsgType>
// 							<Content><![CDATA[%s]]></Content>
// 							<FuncFlag>0</FuncFlag>
// 							</xml>";
// 			if(!empty( $keyword ))
// 			{
// 				$msgType = "text";
// 				$contentStr = "Welcome to wechat world!";
// 				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
// 				echo $resultStr;
// 			}else{
// 				echo "Input something...";
// 			}

		}else {
			echo "";
			exit;
		}
	}
	
	private function checkSignature()
	{
		// you must define TOKEN by yourself
		if (!defined("TOKEN")) {
			throw new Exception('TOKEN is not defined!');
		}

		$signature = isset($_GET["signature"]) ? $_GET["signature"] : "";
		$timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : "";
		$nonce = isset($_GET["nonce"]) ? $_GET['nonce'] : "";
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		// use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */