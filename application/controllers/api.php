<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class api extends CI_Controller {

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
	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('encrypt');
		
		$this->key = '&(*&(*';
		
		//验证˙
		//$this->auth_token();
	}
	
// 	public function index()
// 	{		
// 		$error = "";
// 		$this->load->view('login',$error);
// 	}
	
	public function output_result($code,$message,$data)
	{
		$result = array();
		$result['code'] = $code;
		$result['message'] = $message;
		$result['data'] = $data;
		echo json_encode($result);exit();
	}
	
	public function getUsers()
	{
		$query = $this->db->query("select * from `user`");
		echo json_encode($query->result_array());
	}
	
	public function getUserInfo()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
				
		$query = $this->db->query("select * from `user` where id = {$user_id}");
		if(count($query->result_array()) > 0)
		{
			$this->output_result(0, 'success', $query->result_array()[0]);
		}else{
			$this->output_result(-1, 'failed', '没有该用户');
		}
	}
	
	public function getUserInfoById()
	{
		$user_id = $this->format_get('user_id');
		$query = $this->db->query("select nickname,photo,sex from `user` where id = {$user_id}");
		if(count($query->result_array()) > 0)
		{
			$this->output_result(0, 'success', $query->result_array()[0]);
		}else{
			$this->output_result(-1, 'failed', '没有该用户');
		}
	}
	
	//登陆
	public function login()
	{
		$username = $this->format_get('username');
		$type = $this->format_get('type');
		$authcode = $this->format_get('code');
		$password = md5($this->key.$this->format_get('password'));
		
		$result = $this->db->query("select * from `user` where username = '{$username}' and type = '{$type}'")->result_array();
		
		if(count($result) >= 1)
		{
			$result2 = $this->db->query("select * from `user` where username = '{$username}' and password='{$password}' and type = '{$type}'")->result_array();
			if(count($result2) >= 1)
			{
				$array['id'] = $this->encrypt->encode($result2[0]['id'], $this->key);
				$array['nickname'] = $result2[0]['nickname'];
				$array['phone'] = $result2[0]['username'];
				$array['photo'] = $result2[0]['photo'];
				$this->output_result(0, 'success', $array);
			}else{
				$this->output_result(-3, 'failed', '密码错误');
			}
		}else{
			$this->output_result(-2, 'failed', '用户不存在');
		}
	}
	
	public function register()
	{
		$username = $this->format_get('username');
		$auth_code_secret = $this->encrypt->decode($this->format_get('auth_code_secret'),$this->key);
		$type = $this->format_get('type');
		$authcode = $this->format_get('code');
		if($authcode != $auth_code_secret)
		{
			$this->output_result(-1, 'failed', '验证码错误');
		}
		$result = $this->db->query("select * from `user` where username = '{$username}' and type = '{$type}'")->result_array();
		
		if(count($result) >= 1)
		{
			$this->output_result(-1, 'failed', '该用户已注册');
		}
		else
		{
// 			$create_time = time();
// 			$userinfo = $this->db->query(" insert into `user` (username,type,create_time) VALUES ('{$username}','{$type}','{$create_time}')");
// 			$userid = $this->db->insert_id();
// 			//echo $this->encrypt->decode($userid, $this->key);
// 			if($type == "lawyer")
// 			{
// 				$this->db->query(" insert into `lawyer` (userid) VALUES ('{$userid}')");
// 			}else if($type == "victim")
// 			{
// 				$this->db->query(" insert into `victim` (userid) VALUES ('{$userid}')");
// 			}
			$this->output_result(0, 'success', '');
		}
	}
	
	public function get_authcode()
	{
		$mobile = $this->format_get('mobile');
		$authcode = mt_rand(111111, 999999);
		$_SESSION['authcode'] = $authcode;
		$this->session->set_userdata('authcode', $authcode);
		$result = $this->sms_code($mobile, $authcode);
		$this->output_result(0, 'success', $this->encrypt->encode($authcode,$this->key));
	}
	
	function upload_user_photo()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$config['upload_path'] = getcwd().'/uploads/user/';
		$config['file_name'] = 'user_' . random_string() . '-' .  $user_id;
		$config['allowed_types'] = 'gif|jpg|png';
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ( ! $this->upload->do_upload('user_image'))
		{
			$data['log'] = $this->upload->display_errors();
			$data['create_time'] = time();
			$this->db->insert('log',$data);
			$this->output_result(-1, 'failed', $this->upload->display_errors());
		}
		else
		{
			$photo = '/uploads/user/' . $this->upload->data()['file_name'];
		}
		$this->db->query("update `user` set photo='{$photo}' where id='{$user_id}'");
		$this->output_result(0, 'success',$photo);
	}
	
	public function complete_userinfo()
	{
		//$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$username = $this->format_get('username');
		$type = $this->format_get('type');
		$nickname = $this->format_get('nickname');
		$password1 = $this->format_get('password1');
		$password2 = $this->format_get('password2');
		
		if($password1 != $password2 || $password1 == '')
		{
			$this->output_result(-1, 'failed', '密码不一致');
		}else if ($nickname == ''){
			$this->output_result(-2, 'failed', '昵称不能为空');
		}else{
			$create_time = time();
			$password = md5($this->key.$password1);
			$userinfo = $this->db->query(" insert into `user` (username,nickname,password,type,create_time) VALUES ('{$username}','{$nickname}','{$password}','{$type}','{$create_time}')");
			$userid = $this->db->insert_id();
			if($type == "lawyer")
				{
					$this->db->query(" insert into `lawyer` (userid) VALUES ('{$userid}')");
				}else if($type == "victim")
					{
					$this->db->query(" insert into `victim` (userid) VALUES ('{$userid}')");
				}
			$this->output_result(0, 'success', $this->encrypt->encode($userid, $this->key));
				
			//$this->db->query("update `user` set password='{$password}' and nickname='{$nickname}' where userid='{$user_id}'");
		}
	}
	
	public function get_messages()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$page = addslashes($_GET['page']);
		$number = addslashes($_GET['number']);
		$start = ($page-1) * $number;
		$query = $this->db->query("select * from `message` where user_id={$user_id} order by create_time desc limit {$start},{$number}");
		$this->output_result(0, 'success', $query->result_array());
	}
	
	public function get_activity()
	{
		$activity_id = $this->format_get('id');
		$latitude = addslashes($_GET['latitude']);
		$longitude = addslashes($_GET['longitude']);
		$result = $this->db->query("select t1.*,t2.*,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance'
					from `activity` t1 left join `user` t2 on t1.creater_id = t2.id where t1.id = '{$activity_id}'")->result_array()[0];
		$this->output_result(0, 'success', $result);
	}
	
	function create_activity()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$data['time'] = str_replace("+", " ", $this->format_get('time'));
		$data['address'] = str_replace("+", " ", $this->format_get('address'));
		$data['remain_number'] = $this->format_get('remain_number');
		$data['sex_limit'] = $this->format_get('sex_limit');
		$data['memo'] = $this->format_get('memo');
		$data['create_time'] = time();
		$data['creater_id'] = $user_id;
		$data['category'] = $this->format_get('category');
		$data['longitude'] = $this->format_get('longitude');
		$data['latitude'] = $this->format_get('latitude');
		
		
		$this->db->insert('activity',$data);
		$this->db->insert_id();

		$this->output_result(0, 'success', $this->db->insert_id());
	}
	
	
	
	function report_activity()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$data['activity_id'] = $this->format_get('activity_id');
		$data['user_id'] = $user_id;
		$data['create_time'] = time();
		
		$result = $this->db->query("select * from `report` where user_id={$user_id} and activity_id={$data['activity_id']}")->result_array();
		if(count($result) > 0)
		{
			$this->output_result(0, 'success', "已举报该活动");
		}else{
			$this->db->insert('report',$data);
			$this->output_result(0, 'success', "举报成功");
		}
	}
	
	function join_activity()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$data['activity_id'] = $this->format_get('activity_id');
		$data['user_id'] = $user_id;
		$data['create_time'] = time();
		
		$result = $this->db->query("select * from `attend` where user_id={$user_id} and activity_id={$data['activity_id']}")->result_array();
		if(count($result) > 0)	
		{
			$this->output_result(0, 'success', "已报名该活动");
		}else{
			$this->db->insert('attend',$data);
			$this->output_result(0, 'success', "报名成功");
		}
	}
	
	function collect_activity()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$data['activity_id'] = $this->format_get('activity_id');
		$data['user_id'] = $user_id;
		$data['create_time'] = time();
	
		$result = $this->db->query("select * from `collect` where user_id={$user_id} and activity_id={$data['activity_id']}")->result_array();
		if(count($result) > 0)
		{
			$this->output_result(0, 'success', "已收藏该活动");
		}else{
			$this->db->insert('collect',$data);
			$this->output_result(0, 'success', "收藏成功");
		}
	
	}
	
	
    function get_activities()
	{
		$page = addslashes($_GET['page']);
		$number = addslashes($_GET['number']);
		$order = addslashes($_GET['order']);
		$category = addslashes($_GET['category']);
		$start = ($page-1) * $number;
		$latitude = addslashes($_GET['latitude']);
		$longitude = addslashes($_GET['longitude']);
		
		
// 		"select t1.id,t1.title,t1.product,t1.team_price,t1.market_price,t1.image,t1.now_number, t2.title as partnername,t1.summary,
// 		sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t2.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$longitude} * 0.01745329252 - t2.latitude * 0.01745329252)),2)) as 'distance'
// 		from `team` t1 left join `partner` t2 on t1.partner_id = t2.id
// 		left join `category` t3 on t3.id = t1.group_id
// 		where t1.end_time>unix_timestamp(now())"
		if($category == "所有活动")
		{
			$query = $this->db->query(
					"select t1.*,t2.photo,t2.nickname,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance'
					from `activity` t1 left join `user` t2 on t1.creater_id = t2.id  order by {$order} limit {$start},{$number}");
		}else{
			$query = $this->db->query(
					"select t1.*,t2.photo,t2.nickname,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance'
					from `activity` t1 left join `user` t2 on t1.creater_id = t2.id where category='{$category}'  order by {$order} limit {$start},{$number}");
		}
		
		$this->output_result(0, 'success', $query->result_array());
	}
	
	public function get_activity_by_creater()
	{
			$userid = $this->encrypt->decode($this->format_get('user_id'), $this->key);
			$page = addslashes($_GET['page']);
			$number = addslashes($_GET['number']);
			$latitude = addslashes($_GET['latitude']);
			$longitude = addslashes($_GET['longitude']);
			$start = ($page-1) * $number;
			$query = $this->db->query(
					"select t1.*,t2.photo,t2.nickname,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t1.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t1.latitude * 0.01745329252)),2)) as 'distance'
					from `activity` t1 left join `user` t2 on t1.creater_id = t2.id where t1.creater_id='{$userid}' order by t1.create_time desc limit {$start},{$number}");
			$this->output_result(0, 'success', $query->result_array());
	}
	
	public function get_activity_by_attend()
	{
		$userid = $this->encrypt->decode($this->format_get('user_id'), $this->key);
		$page = addslashes($_GET['page']);
		$number = addslashes($_GET['number']);
		$latitude = addslashes($_GET['latitude']);
		$longitude = addslashes($_GET['longitude']);
		$start = ($page-1) * $number;
		$query = $this->db->query("select t2.*,t3.photo,t3.nickname,
					sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t2.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t2.latitude * 0.01745329252)),2)) as 'distance'
					from `attend` t1 join `activity` t2 on t1.activity_id=t2.id join `user` t3 on t3.id = t2.creater_id  where t1.user_id='{$userid}' order by t2.create_time desc limit {$start},{$number}");
		$this->output_result(0, 'success', $query->result_array());
	}
	
	public function get_activity_by_collect()
	{
		$userid = $this->encrypt->decode($this->format_get('user_id'), $this->key);
		$page = addslashes($_GET['page']);
		$number = addslashes($_GET['number']);
		$latitude = addslashes($_GET['latitude']);
		$longitude = addslashes($_GET['longitude']);
		$start = ($page-1) * $number;
		$query = $this->db->query("select t2.*,t3.photo,t3.nickname,
				sqrt(POW((6370693.5 * cos({$latitude} * 0.01745329252) * ({$longitude} * 0.01745329252 - t2.longitude * 0.01745329252)),2) + POW((6370693.5 * ({$latitude} * 0.01745329252 - t2.latitude * 0.01745329252)),2)) as 'distance'
				from `collect` t1 join `activity` t2 on t1.activity_id=t2.id join `user` t3 on t3.id = t2.creater_id  where t1.user_id='{$userid}' order by t2.create_time desc limit {$start},{$number}");
				$this->output_result(0, 'success', $query->result_array());
	}
	
	private function sms_code($mobile, $code) {
		$content = "【公盛科技】您的验证码是{$code}";
		$url="http://yunpian.com/v1/sms/send.json";
		$encoded_text = urlencode("$content");
		$post_string="apikey=355e91e02a95574559ebba5a3c1af6c2&text=$content&mobile=$mobile";
		return $this->sock_post($url, $post_string);
	}
	
	/**
	 * url 为服务的url地址
	 * query 为请求串
	 */
	function sock_post($url,$query){
		$data = "";
		$info=parse_url($url);
		$fp=fsockopen($info["host"],80,$errno,$errstr,30);
		if(!$fp){
			return $data;
		}
		$head="POST ".$info['path']." HTTP/1.0\r\n";
		$head.="Host: ".$info['host']."\r\n";
		$head.="Referer: http://".$info['host'].$info['path']."\r\n";
		$head.="Content-type: application/x-www-form-urlencoded\r\n";
		$head.="Content-Length: ".strlen(trim($query))."\r\n";
		$head.="\r\n";
		$head.=trim($query);
		$write=fputs($fp,$head);
		$header = "";
		while ($str = trim(fgets($fp,4096))) {
			$header.=$str;
		}
		while (!feof($fp)) {
			$data .= fgets($fp,4096);
		}
		return $data;
	}
	
// 	function send_sms_by_api( $text, $mobile){
// 		$url="http://yunpian.com/v1/sms/send.json";
// 		$encoded_text = urlencode("$text");
// 		$post_string="apikey=355e91e02a95574559ebba5a3c1af6c2&text=$encoded_text&mobile=$mobile";
// 		return sock_post($url, $post_string);
// 	}
	
	
// 	private function pay()
// 	{
// 		$payment_id = addslashes($_GET['payment_id']);
// 		$query = $this->db->query("update `payment` (status) values ('已付款') where id = ");
// 	}
	
	private function get_first_payment_rate()
	{
		$query = $this->db->query("select * from `config` where key = 'first_payment_rate'");
		if(count($query->result_array()) > 0)
		{
			return $query->result_array()[0]['value'];
		}else{
			return 0;
		}
	}
	
	private function get_second_payment_rate()
	{
		$query = $this->db->query("select * from `config` where key = 'second_payment_rate'");
		if(count($query->result_array()) > 0)
		{
			return $query->result_array()[0]['value'];
		}else{
			return 0;
		}
	}
	
	private function format_get($param,$default = "")
	{
		return (isset($_GET[$param]) && $_GET[$param] != "") ? urldecode(addslashes(str_replace('+','%2B',urlencode($_GET[$param])))) : $default;
	}
	
	private function format_post($param,$default = "")
	{
		return (isset($_POST[$param]) && $_POST[$param] != "") ? urldecode(addslashes(str_replace('+','%2B',urlencode($_POST[$param])))) : $default;
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
