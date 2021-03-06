<?php
require_once 'Notification.php';
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class api extends CI_Controller {
	
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * http://example.com/index.php/welcome
	 * - or -
	 * http://example.com/index.php/welcome/index
	 * - or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * 
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct() {
		parent::__construct ();
		$this->load->library ( 'session' );
		$this->load->database ();
		$this->load->helper ( 'url' );
		$this->load->library ( 'encrypt' );
		
		$this->key = '&(*&(*';
		$this->UmengKey = "5710924be0f55a8aba000646";
		$this->UmengSecret = "vuqkrc5tc08uolcgvolti87y60uhieyc";
		
		// 验证˙
		// $this->auth_token();
	}
	
	// public function index()
	// {
	// $error = "";
	// $this->load->view('login',$error);
	// }
	public function output_result($code, $message, $data) {
		$result = array ();
		$result ['code'] = $code;
		$result ['message'] = $message;
		$result ['data'] = $data;
		echo json_encode ( $result );
		exit ();
	}
	public function getUsers() {
		$query = $this->db->query ( "select * from `user`" );
		echo json_encode ( $query->result_array () );
	}
	
	// public function getUserInfo()
	// {
	// $user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
	
	// $query = $this->db->query("select photo,nickname,sex,id from `user` where id = {$user_id}");
	// if(count($query->result_array()) > 0)
	// {
	// $this->output_result(0, 'success', $query->result_array()[0]);
	// }else{
	// $this->output_result(-1, 'failed', '没有该用户');
	// }
	// }
	public function getUserInfoById() {
		$user_id = $this->format_get ( 'user_id' );
		
		$query = $this->db->query ( "select id, nickname,photo,sex,interest from `user` where id = {$user_id}" );
		if (count ( $query->result_array () ) > 0) {
			
			$result = $query->result_array ()[0];
			$result['no_secret_id'] = $result['id'];
			if ($this->format_get ( 'self_user_id', '' ) != '') {
				$result ['id'] = $this->encrypt->encode ( $result ['id'], $this->key );
				$self_user_id = $this->encrypt->decode ( $this->format_get ( 'self_user_id' ), $this->key );
				$query2 = $this->db->query ( "select * from `follow` where follow_user_id={$self_user_id} and followed_user_id={$user_id} and status=1" );
				if (count ( $query2->result_array () ) > 0) {
					$result ['follow'] = "关注中";
				} else {
					$result ['follow'] = "关注";
				}
			} else {
				$result ['follow'] = "关注";
			}
			$this->output_result ( 0, 'success', $result );
		} else {
			$this->output_result ( - 1, 'failed', '没有该用户' );
		}
	}
	
	// 登陆
	public function login() {
		$username = $this->format_get ( 'username' );
		$authcode = $this->format_get ( 'code' );
		$password = md5 ( $this->key . $this->format_get ( 'password' ) );
		
		$result = $this->db->query ( "select * from `user` where username = '{$username}'" )->result_array ();
		
		if (count ( $result ) >= 1) {
			$result2 = $this->db->query ( "select * from `user` where username = '{$username}' and password='{$password}'" )->result_array ();
			if (count ( $result2 ) >= 1) {
				$array ['id'] = $this->encrypt->encode ( $result2 [0] ['id'], $this->key );
				$array ['nickname'] = $result2 [0] ['nickname'];
				$array ['phone'] = $result2 [0] ['username'];
				$array ['photo'] = $result2 [0] ['photo'];
				$array ['sex'] = $result2 [0] ['sex'];
				$array ['device_token'] = $result2 [0] ['device_token'];
				$array ['interest'] = $result2 [0] ['interest'];
				$array ['no_secret_id'] = $result2 [0] ['id'];
				$this->output_result ( 0, 'success', $array );
			} else {
				$this->output_result ( - 3, 'failed', '密码错误' );
			}
		} else {
			$this->output_result ( - 2, 'failed', '用户不存在' );
		}
	}
	public function register() {
		$username = $this->format_get ( 'username' );
		$auth_code_secret = $this->encrypt->decode ( $this->format_get ( 'auth_code_secret' ), $this->key );
		$authcode = $this->format_get ( 'code' );
		if ($authcode != $auth_code_secret) {
			$this->output_result ( - 1, 'failed', '验证码错误' );
		}
		$result = $this->db->query ( "select * from `user` where username = '{$username}'" )->result_array ();
		
		if (count ( $result ) >= 1) {
			$this->output_result ( - 1, 'failed', '该用户已注册' );
		} else {
			$this->output_result ( 0, 'success', '' );
		}
	}
	
	public function login_authcode(){
		$auth_code_secret = $this->encrypt->decode ( $this->format_get ( 'auth_code_secret' ), $this->key );
		$authcode = $this->format_get ( 'code' );	
		$username = $this->encrypt->decode ( $this->format_get ( 'username' ), $this->key );
		
		//$username = $this->format_get ( 'username' );
		
		if($auth_code_secret == $authcode)
		{
			$result = $this->db->query ( "select * from `user` where username = '{$username}'" )->result_array ();
			if(count($result) > 0)
			{
				$result[0] ['id'] = $this->encrypt->encode ( $result[0] ['id'], $this->key );
				$this->output_result(0, 'success', $result[0]);
			}else{
				$this->output_result(-1, 'failed', '该手机号不存在，请先注册');
			}
		}else{
			$this->output_result(-1, 'failed', '验证码错误');
		}
	}
	
	public function check_username()
	{
		
		$username = $this->format_get ( 'username' );
		$result = $this->db->query ( "select * from `user` where username = '{$username}'" )->result_array ();
		if(count($result) == 0)
		{
			$this->output_result(0, 'success', $this->encrypt->encode ( $username, $this->key ));
		}else{
			$this->output_result(-1, 'failed', '该手机号已注册');
		}
	}
	
	public function register_authcode(){
		$auth_code_secret = $this->encrypt->decode ( $this->format_get ( 'auth_code_secret' ), $this->key );
		$authcode = $this->format_get ( 'code' );
		$device_token = $this->format_get ( 'device_token' );
		$username_secret = $this->encrypt->decode ( $this->format_get ( 'username_secret' ), $this->key );
		
		$username = $this->format_get ( 'username' );
	
		if($username_secret != $username)
		{
			$this->output_result(-1, 'failed', '非法请求');
		}
		if($auth_code_secret == $authcode)
		{
			$result = $this->db->query ( "select * from `user` where username = '{$username}'" )->result_array ();
			if(count($result) == 0)
			{
				$result2 = $this->db->query ( "select * from `user` where device_token = '{$device_token}'" )->result_array ();
				if(count($result2) > 0)
				{
					$time = time();
					$this->db->query("update `user` set username='{username},create_time='$time' where device_token='{$device_token}'");
					$id = $this->encrypt->encode ( $result[0]['id'], $this->key );
				}else{
					$data['username'] = $username;
					$data['create_time'] = time();
					$this->db->insert('user',$data);
					$id = $this->encrypt->encode ( $this->db->insert_id (), $this->key );
				}
				$this->output_result(0, 'success', $id);
			}else{
				$this->output_result(-1, 'failed', '该手机号已注册');
			}
		}else{
			$this->output_result(-1, 'failed', '验证码错误');
		}
	}
	
	public function register_device()
	{
		$device_token = $this->format_get ( 'device_token' );
		$result2 = $this->db->query ( "select * from `user` where device_token = '{$device_token}'" )->result_array ();
		
	}
	
	public function get_authcode() {
		$mobile = $this->format_get ( 'mobile' );
		$authcode = mt_rand ( 111111, 999999 );
		$result = $this->sms_code ( $mobile, $authcode );
		$res['username'] = $this->encrypt->encode ( $mobile, $this->key );
		$res['authcode'] = $this->encrypt->encode ( $authcode, $this->key );
		$this->output_result ( 0, 'success', $res);
	}
	
	public function register_get_authcode() {
		$mobile = $this->format_get ( 'mobile' );
		$authcode = mt_rand ( 111111, 999999 );
		$result = $this->sms_code ( $mobile, $authcode );
	
		$res['username'] = $this->encrypt->encode ( $mobile, $this->key );
		$res['authcode'] = $this->encrypt->encode ( $authcode, $this->key );
	
		$result = $this->db->query ( "select * from `user` where username = '{$mobile}'" )->result_array ();
		if(count($result) == 0)
		{
			$this->output_result(0, 'success', $res);
		}else{
			$this->output_result(-1, 'failed', '该手机号已注册');
		}
	}
	
	function upload_user_photo() {
		$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$config ['upload_path'] = getcwd () . '/uploads/user/';
		$config ['file_name'] = 'user_' . random_string () . '-' . $user_id;
		$config ['allowed_types'] = 'gif|jpg|png';
		$this->load->library ( 'upload', $config );
		$this->upload->initialize ( $config );
		if (! $this->upload->do_upload ( 'user_image' )) {
			$data ['log'] = $this->upload->display_errors ();
			$data ['create_time'] = time ();
			$this->db->insert ( 'log', $data );
			$this->output_result ( - 1, 'failed', $this->upload->display_errors () );
		} else {
			$photo = '/uploads/user/' . $this->upload->data ()['file_name'];
		}
		$this->db->query ( "update `user` set photo='{$photo}' where id='{$user_id}'" );
		$this->output_result ( 0, 'success', $photo );
	}
	
	public  function change_interest() {
		$userid = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$interest = $this->format_get('interest');
		$result = $this->db->query("select * from `user` where id={$userid}")->result_array();
		if(count($result) > 0)
		{
			$this->db->query("update `user` set interest='{$interest}' where id={$userid}");
			$this->output_result ( 0, 'success', '修改成功' );
		}else{
			$this->output_result ( 0, 'success', '用户不存在' );
		}
	}
	
	public function get_categories()
	{
		$result = $this->db->query("select * from `category` where is_delete=0")->result_array();
		$this->output_result ( 0, 'success', $result );
	}
	
	public  function change_nickname() {
		$userid = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$nickname = $this->format_get('nickname');
		$result = $this->db->query("select * from `user` where id={$userid}")->result_array();
		if(count($result) > 0)
		{
			$this->db->query("update `user` set nickname='{$nickname}' where id={$userid}");
			$this->output_result ( 0, 'success', '修改成功' );
		}else{
			$this->output_result ( 0, 'success', '用户不存在' );
		}
	}
	
	public function complete_userinfo() {
		$userid = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$nickname = $this->format_get ( 'nickname' );
		$sex = $this->format_get ( 'sex' );
		$interest = $this->format_get ( 'interest' );
		
		if ($sex != "男" && $sex != "女") {
			$this->output_result ( - 1, 'failed', '性别输入有误' );
		} else if ($nickname == '') {
			$this->output_result ( - 2, 'failed', '昵称不能为空' );
		} else if ($interest == '') {
			$this->output_result ( - 3, 'failed', '兴趣不能为空' );
		} 
		else {
			$create_time = time ();
			$userinfo = $this->db->query ( "update `user` set sex='{$sex}',interest='{$interest}',nickname='{$nickname}',create_time='{$create_time}' where id={$userid} " );
			$result = $this->db->query ( "select id, nickname,photo,sex,interest from `user` where id = {$userid}" )->result_array();
			$result[0] ['no_secret_id'] = $result[0] ['id'];
			$result[0] ['id'] = $this->encrypt->encode ( $result[0] ['id'], $this->key );
			 
			$this->output_result(0, 'success', $result[0]);
			// $this->db->query("update `user` set password='{$password}' and nickname='{$nickname}' where userid='{$user_id}'");
		}
	}
	
	public function set_password() {
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$password1 = $this->format_get ( 'password1' );
		$password2 = $this->format_get ( 'password2' );
	
		if ($password1 != $password2 || $password1 == '') {
			$this->output_result ( - 1, 'failed', '密码不一致' );
		}else {
			$create_time = time ();
			$password = md5 ( $this->key . $password1 );
			$this->db->query("update `user` set password='{$password}' where id={$user_id}");
			$this->output_result ( 0, 'success', '设置成功' );
			// $this->db->query("update `user` set password='{$password}' and nickname='{$nickname}' where userid='{$user_id}'");
		}
	}
	
// 	public function get_messages() {
// 		$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
// 		$page = addslashes ( $_GET ['page'] );
// 		$number = addslashes ( $_GET ['number'] );
// 		$start = ($page - 1) * $number;
// 		$query = $this->db->query ( "select * from `message` where user_id={$user_id} order by create_time desc limit {$start},{$number}" );
// 		$this->output_result ( 0, 'success', $query->result_array () );
// 	}
	public function get_activity() {
		$activity_id = $this->format_get ( 'id' );
		if ($this->format_get ( 'user_id' ) == "0") {
			$user_id = "0";
		} else {
			$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		}
		
		$latitude = addslashes ( $_GET ['latitude'] );
		$longitude = addslashes ( $_GET ['longitude'] );
		$result = $this->db->query ( "select t1.*,t2.photo,t2.nickname,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance'
					from `activity` t1 left join `user` t2 on t1.creater_id = t2.id where t1.id = '{$activity_id}'" )->result_array ()[0];
		$result ["apply_number"] = $this->db->query ( "select count(id) as count from `attend` where activity_id = {$activity_id}" )->result_array ()[0]['count'];
		
		$apply = $this->db->query ( "select * from `attend` where activity_id = {$activity_id} and user_id={$user_id}" )->result_array ();
		$result ["is_apply"] = count ( $apply ) > 0 ? "1" : "0";
		
		$collect = $this->db->query ( "select * from `collect` where activity_id = {$activity_id} and user_id={$user_id}" )->result_array ();
		$result ["is_collect"] = count ( $collect ) > 0 ? "1" : "0";
		
		$this->output_result ( 0, 'success', $result );
	}
	
	public function get_recommand_place() {
		$category = $this->format_get ( 'category' );
		$latitude = addslashes ( $_GET ['latitude'] );
		$longitude = addslashes ( $_GET ['longitude'] );
		
		$result = $this->db->query ( "
				select t2.address as name FROM
				(
				select t1.address,t1.cover_distance,
				sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance'
				from `place` t1 where category='{$category}' and datediff(t1.expire_date,now()) >= 0
				order by level
				) t2
				where  t2.distance < t2.cover_distance
				limit 1
				" )->result_array ();
		if(count($result) > 0)
		{
			$this->output_result ( 0, 'success', $result[0]['name'] );
		}else{
			$this->output_result ( 0, 'success', "" );
		}
	
				
	}
	
	function create_activity() {
		$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$data ['time'] = str_replace ( "+", "-", $this->format_get ( 'time' ) );
		$data ['address'] = str_replace ( "+", " ", $this->format_get ( 'address' ) );
		$data ['remain_number'] = $this->format_get ( 'remain_number' );
		$data ['sex_limit'] = $this->format_get ( 'sex_limit' );
		$data ['memo'] = $this->format_get ( 'memo' );
		$data ['create_time'] = time ();
		$data ['creater_id'] = $user_id;
		$data ['category'] = $this->format_get ( 'category' );
		$data ['longitude'] = $this->format_get ( 'longitude' );
		$data ['latitude'] = $this->format_get ( 'latitude' );
		
		$this->db->insert ( 'activity', $data );
		$this->db->insert_id ();
		
		$this->output_result ( 0, 'success', $this->db->insert_id () );
		
	}
	
	function test_notification()
	{
		$notification = new Notification($this->UmengKey, $this->UmengSecret);
		$notification->sendIOSListcast("***在你周边发起了羽毛球活动", "33992a3218007b7323f207f296b4873fc20c40684c3fa41e089d15fdf6e1cd01");
		// $demo = new Demo("your appkey", "your app master secret");
		// $demo->sendAndroidUnicast();
	}
	
	function report_activity() {
		$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$data ['activity_id'] = $this->format_get ( 'activity_id' );
		$data ['content'] = $this->format_get ( 'content' );
		$data ['user_id'] = $user_id;
		$data ['create_time'] = time ();
		
		$result = $this->db->query ( "select * from `report` where user_id={$user_id} and activity_id={$data['activity_id']}" )->result_array ();
		if (count ( $result ) > 0) {
			$this->output_result ( -1, 'failed', "已举报该活动" );
		} else {
			$this->db->insert ( 'report', $data );
			$this->output_result ( 0, 'success', "举报成功" );
		}
	}
	function join_activity() {
		$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$data ['activity_id'] = $this->format_get ( 'activity_id' );
		$data ['user_id'] = $user_id;
		$data ['create_time'] = time ();
		$res = $this->db->query ( "select * from `activity` where creater_id={$user_id} and id={$data['activity_id']}" )->result_array ();
		if (count ( $res ) > 0) {
			$this->output_result ( -1, 'failed', "这活动是你自己发起的！" );
		}
		$number_res = $this->db->query ( "select * from `activity` where id={$data['activity_id']} and apply_number >= remain_number" )->result_array ();
		if (count ( $number_res ) > 0) {
			$this->output_result ( -2, 'failed', "活动名额已满，换一个吧" );
		}
		
		$result = $this->db->query ( "select * from `attend` where user_id={$user_id} and activity_id={$data['activity_id']}" )->result_array ();
		if (count ( $result ) > 0) {
			$this->output_result ( 0, 'success', "已报名该活动" );
		} else {
			$r = $this->db->query ( "select * from `activity` where id={$data['activity_id']}" )->result_array ()[0];
			if($r['sex_limit'] != "不限性别")
			{
				$user_sex = $this->db->query ( "select * from `user` where id={$data['user_id']}" )->result_array ()[0]['sex'];
				if(mb_substr($r['sex_limit'], 1,1,'utf-8') == $user_sex)
				{
					$this->db->insert ( 'attend', $data );
					$this->db->query ( "update `activity` set apply_number = apply_number + 1 where id={$data['activity_id']}" );
					$this->output_result ( 0, 'success', "报名成功" );
				}else{
					$this->output_result ( -1, 'failed', "该项目只".$r['sex_limit'].",嘿嘿" );
				}	
			}
			
			$this->db->insert ( 'attend', $data );
			$this->db->query ( "update `activity` set apply_number = apply_number + 1 where id={$data['activity_id']}" );
			$this->output_result ( 0, 'success', "报名成功" );
		}
	}
	function collect_activity() {
		$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$data ['activity_id'] = $this->format_get ( 'activity_id' );
		$data ['user_id'] = $user_id;
		$data ['create_time'] = time ();
		
		$result = $this->db->query ( "select * from `collect` where user_id={$user_id} and activity_id={$data['activity_id']}" )->result_array ();
		if (count ( $result ) > 0) {
			$this->output_result ( 0, 'success', "已收藏该活动" );
		} else {
			$this->db->insert ( 'collect', $data );
			$this->output_result ( 0, 'success', "收藏成功" );
		}
	}
	function get_apply_users() {
		$activity_id = $this->format_get ( 'activity_id' );
		$result = $this->db->query ( "select t2.photo,t2.nickname,t2.id,t2.id as no_secret_id,t2.sex from `attend` t1 join `user` t2 on t1.user_id=t2.id  where activity_id={$activity_id}" )->result_array ();
		$this->output_result ( 0, 'success', $result );
	}
	function get_activities() {
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$search_text = addslashes ( $_GET ['search_text'] );
		$time = addslashes ( $_GET ['time'] );
		$category = addslashes ( $_GET ['category'] );
		$start = ($page - 1) * $number;
		$latitude = addslashes ( $_GET ['latitude'] );
		$longitude = addslashes ( $_GET ['longitude'] );
		$query_str = "select t1.*,t2.photo,t2.nickname,t2.sex,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance',t1.remain_number - t1.apply_number as number
					from `activity` t1 left join `user` t2 on t1.creater_id = t2.id";
		
		// if ($time == "今天") {
		// 	$query_str .= " DATEDIFF(t1.time,NOW()) = 0";
		// } else if ($time == "明天") {
		// 	$query_str .= " DATEDIFF(t1.time,NOW()) = 1";
		// } else if ($time == "后天") {
		// 	$query_str .= " DATEDIFF(t1.time,NOW()) = 2";
		// } else if ($time == "一周内") {
		// 	$query_str .= " DATEDIFF(t1.time,NOW()) <= 7";
		// } else if ($time == "一个月内") {
		// 	$query_str .= " DATEDIFF(t1.time,NOW()) <= 30";
		// }
		// $query_str .= " and DATEDIFF(t1.time,NOW()) > -1";
		if ($category != "所有活动") {
			$query_str .= " where category in ('{$category}')";
		}else{
			$query_str .= " where 1=1";
		}
		//$query_str .= " and t1.apply_number <> t1.remain_number";
		$query_str .= " and t1.is_delete=0";
		//$query_str .= " and distance <= 50000";
		if($search_text != "")
		{
			$query_str .= " and (t2.nickname like '%{$search_text}%' or t1.memo like '%{$search_text}%' or t1.address like '%{$search_text}%')";
		}
		$query_str .= " order by number desc, distance asc, t1.time asc limit {$start},{$number}";
		$query = $this->db->query ( $query_str );
		
		$this->output_result ( 0, 'success', $query->result_array () );
	}
	public function get_activity_by_creater() {
		$userid = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$latitude = addslashes ( $_GET ['latitude'] );
		$longitude = addslashes ( $_GET ['longitude'] );
		$start = ($page - 1) * $number;
		$query = $this->db->query ( "select t1.*,t2.photo,t2.nickname,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance'
					from `activity` t1 left join `user` t2 on t1.creater_id = t2.id where t1.creater_id='{$userid}' order by t1.create_time desc limit {$start},{$number}" );
		$this->output_result ( 0, 'success', $query->result_array () );
	}
	public function get_activity_by_attend() {
		$userid = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$latitude = addslashes ( $_GET ['latitude'] );
		$longitude = addslashes ( $_GET ['longitude'] );
		$start = ($page - 1) * $number;
		$query = $this->db->query ( "select t2.*,t3.photo,t3.nickname,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t2.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t2.latitude * 0.01745329252)),2)) as 'distance'
					from `attend` t1 join `activity` t2 on t1.activity_id=t2.id join `user` t3 on t3.id = t2.creater_id  where t1.user_id='{$userid}' order by t2.create_time desc limit {$start},{$number}" );
		$this->output_result ( 0, 'success', $query->result_array () );
	}
	public function get_activity_by_collect() {
		$userid = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$latitude = addslashes ( $_GET ['latitude'] );
		$longitude = addslashes ( $_GET ['longitude'] );
		$start = ($page - 1) * $number;
		$query = $this->db->query ( "select t2.*,t3.photo,t3.nickname,
				sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t2.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t2.latitude * 0.01745329252)),2)) as 'distance'
				from `collect` t1 join `activity` t2 on t1.activity_id=t2.id join `user` t3 on t3.id = t2.creater_id  where t1.user_id='{$userid}' order by t2.create_time desc limit {$start},{$number}" );
		$this->output_result ( 0, 'success', $query->result_array () );
	}
	
	public function delete_activity() {
		$userid = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$activity_id = $this->format_get('activity_id');
		$delete_reason = $this->format_get('delete_reason');
		$this->db->query("update `activity` set is_delete=1,delete_reason='{$delete_reason}' where id={$activity_id} and creater_id={$userid} ");
		$this->output_result ( 0, 'success', '删除成功' );
	}
	
	
	
	public function follow() {
		$follow_user_id = $this->encrypt->decode ( $this->format_get ( 'self_user_id' ), $this->key );
		$followed_user_id = addslashes ( $_GET ['user_id'] );
		$query = $this->db->query ( "select * from `follow` where follow_user_id={$follow_user_id} and followed_user_id={$followed_user_id}" )->result_array ();
		if (count ( $query ) > 0) {
			$this->db->query ( "update `follow` set status=1 where follow_user_id={$follow_user_id} and followed_user_id={$followed_user_id}" );
			$this->output_result ( 0, 'success', '成功关注' );
		} else {
			$data ['follow_user_id'] = $follow_user_id;
			$data ['followed_user_id'] = $followed_user_id;
			$data ['create_time'] = time ();
			$data ['status'] = 1;
			$this->db->insert ( 'follow', $data );
			$this->output_result ( 0, 'success', '成功关注' );
		}
	}
	public function cancel_follow() {
		$follow_user_id = $this->encrypt->decode ( $this->format_get ( 'self_user_id' ), $this->key );
		$followed_user_id = addslashes ( $_GET ['user_id'] );
		$query = $this->db->query ( "select * from `follow` where follow_user_id={$follow_user_id} and followed_user_id={$followed_user_id}" )->result_array ();
		if (count ( $query ) > 0) {
			$this->db->query ( "update `follow` set status=0 where follow_user_id={$follow_user_id} and followed_user_id={$followed_user_id}" );
			$this->output_result ( 0, 'success', '已取消关注' );
		} else {
			$this->output_result ( 0, 'success', '已取消关注' );
		}
	}
	public function send_message() {
		$data ['content'] = addslashes ( $_GET ['content'] );
		$data ['user_id'] = $this->encrypt->decode ( $this->format_get ( 'self_user_id' ), $this->key );
		$data ['to_user_id'] = addslashes ( $_GET ['user_id'] );
		$data ['create_time'] = time ();
		$this->db->insert ( 'message', $data );
		$this->output_result ( 0, 'success', 'success' );
	}
	public function get_follows() {
		$user_id = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$start = ($page - 1) * $number;
		
		$result = $this->db->query ( "select t2.id,t2.id as no_secret_id, t2.photo,t2.nickname from `follow` t1 left join `user` t2 on t1.followed_user_id=t2.id where follow_user_id={$user_id} and status=1 limit {$start},{$number}" )->result_array ();
		$this->output_result ( 0, 'success', $result );
	}
	public function get_messages() {
		$self_id = $this->encrypt->decode ( $this->format_get ( 'self_user_id' ), $this->key );
		$user_id = $this->format_get ( 'user_id' );
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$start = ($page - 1) * $number;
		
		$query = $this->db->query ( "
			select 
				t2.nickname as user_nickname,
				t2.photo as user_photo,
				t3.nickname as to_user_nickname,
				t3.photo as to_user_photo,
				t1.content,
				t1.create_time 
				from `message` t1 
				left join `user` t2 on t1.user_id=t2.id 
				left join `user` t3 on t1.to_user_id=t3.id 
				where (t1.user_id={$self_id} and t1.to_user_id={$user_id} ) 
				or (t1.user_id={$user_id} and t1.to_user_id={$self_id} )
				order by t1.create_time desc
				limit {$start},{$number}
				" );
		
		$this->output_result ( 0, 'success', $query->result_array () );
	}
	public function get_message_list() {
		$userid = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$start = ($page - 1) * $number;
		
		$query = $this->db->query ( "
				select
				t3.user_id,
				t3.content, 
				t2.nickname,
				t2.photo,
				t3.create_time 
				from 
					(select 
						case when t1.user_id={$userid} then t1.to_user_id else t1.user_id END as user_id,
						t1.create_time,t1.content from 
						(select * from `message` ORDER by create_time DESC) t1 
					where t1.user_id={$userid} or t1.to_user_id={$userid} 
					GROUP by case when user_id>t1.to_user_id THEN t1.user_id*1000000000 + t1.to_user_id ELSE t1.to_user_id*1000000000 + t1.user_id END  
					order by t1.create_time DESC
					) 
				t3 left join `user` t2 on t2.id=t3.user_id
				limit {$start},{$number}
				" );
		$this->output_result ( 0, 'success', $query->result_array () );
	}
	public function get_follow_list() {
		$userid = $this->encrypt->decode ( $this->format_get ( 'user_id' ), $this->key );
		$page = addslashes ( $_GET ['page'] );
		$number = addslashes ( $_GET ['number'] );
		$query = $this->db->query ( "
			select t2.id, t2.nickname,t2.photo from `follow` t1 left join `user` t2 on t1.followed_user_id=t2.id where follow_user_id = {$userid}
		" );
		$this->output_result ( 0, 'success', $query->result_array () );
	}
	private function sms_code($mobile, $code) {
		$content = "【一隼网络】您的验证码是{$code}";
		$url = "http://yunpian.com/v1/sms/send.json";
		$encoded_text = urlencode ( "$content" );
		$post_string = "apikey=355e91e02a95574559ebba5a3c1af6c2&text=$content&mobile=$mobile";
		return $this->sock_post ( $url, $post_string );
	}
	
	/**
	 * url 为服务的url地址
	 * query 为请求串
	 */
	function sock_post($url, $query) {
		$data = "";
		$info = parse_url ( $url );
		$fp = fsockopen ( $info ["host"], 80, $errno, $errstr, 30 );
		if (! $fp) {
			return $data;
		}
		$head = "POST " . $info ['path'] . " HTTP/1.0\r\n";
		$head .= "Host: " . $info ['host'] . "\r\n";
		$head .= "Referer: http://" . $info ['host'] . $info ['path'] . "\r\n";
		$head .= "Content-type: application/x-www-form-urlencoded\r\n";
		$head .= "Content-Length: " . strlen ( trim ( $query ) ) . "\r\n";
		$head .= "\r\n";
		$head .= trim ( $query );
		$write = fputs ( $fp, $head );
		$header = "";
		while ( $str = trim ( fgets ( $fp, 4096 ) ) ) {
			$header .= $str;
		}
		while ( ! feof ( $fp ) ) {
			$data .= fgets ( $fp, 4096 );
		}
		return $data;
	}
	private function format_get($param, $default = "") {
		return (isset ( $_GET [$param] ) && $_GET [$param] != "") ? urldecode ( addslashes ( str_replace ( '+', '%2B', urlencode ( $_GET [$param] ) ) ) ) : $default;
	}
	private function format_post($param, $default = "") {
		return (isset ( $_POST [$param] ) && $_POST [$param] != "") ? urldecode ( addslashes ( str_replace ( '+', '%2B', urlencode ( $_POST [$param] ) ) ) ) : $default;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
