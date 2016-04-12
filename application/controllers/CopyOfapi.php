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
				$this->output_result(0, 'success', $this->encrypt->encode($result2[0]['id'], $this->key));
			}else{
				$this->output_result(-3, 'failed', '密码错误');
			}
		}else{
			$this->output_result(-2, 'failed', '用户不存在');
		}
	}
	
	public function get_lawyer_status()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$result = $this->db->query("select * from lawyer where userid = {$user_id}")->result_array();
		$this->output_result(0, 'success', $result[0]['status']);
	}
	
	public function register()
	{
		$username = $this->format_get('username');
		$type = $this->format_get('type');
		$authcode = $this->format_get('code');
		if($authcode != $this->session->userdata('authcode'))
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
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ( ! $this->upload->do_upload("user_image"))
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
		$this->db->query("update `user` set photo='{$photo}' where userid='{$user_id}'");
		$this->output_result(0, 'success','success');
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
	
	public function get_lawyer_info()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$result = $this->db->query("select * from `lawyer` where userid = '{$user_id}'")->result_array();
		$this->output_result(0, 'success', $result);
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
	
	public function get_case_info()
	{
		$case_id = $this->format_get('case_id');
		$result = $this->db->query("select * from `case` where id = '{$case_id}'")->result_array();
		$this->output_result(0, 'success', $result);
	}
	
	function create_case()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$config['upload_path'] = getcwd().'/uploads/image/';
		$amount = $this->format_get('amount');
		$time = $this->format_get('time');
		$address = $this->format_get('address');
		$department = $this->format_get('department');
		$evidence_ids = $this->format_get('evidence_ids');
		$responsibility = $this->format_get('responsibility');
				
			
		$data['createtime'] = time();
		$data['time'] = $time;
		$data['address'] = $address;
		$data['department'] = $department;
		$data['responsibility'] = $responsibility;
		$data['victim_id'] = $user_id;
		$this->db->insert('case',$data);
		$case_id = $this->db->insert_id();
		
		$this->output_result(0, 'success', $this->db->insert_id());
	}
	
	
	function upload_evidence()
	{
		$config['upload_path'] = getcwd().'/uploads/image/';
		$jsondata = json_decode($this->format_post('data'));
		$case_id = $jsondata->case_id;
		$config['file_name'] = 'case_' . random_string() . '-' . $case_id;
		
		$image_type = $jsondata->image_type;
				
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
// 				$config['max_size'] = '100';
// 				$config['max_width']  = '1024';
// 				$config['max_height']  = '768';
	
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ( ! $this->upload->do_upload("file"))
					{
						$data['log'] = $this->upload->display_errors();
						$data['create_time'] = time();
						$this->db->insert('log',$data);
						$this->output_result(-1, 'failed', $this->upload->display_errors());
					}
					else
					{
						$path =  '/uploads/image/' . $this->upload->data()['file_name'];
						$data['evidence_image'] = $path;
					}
			$data['image_type'] = $image_type;
			$data['case_id'] = $case_id;
			
			$this->db->insert('evidence',$data);
			$this->output_result(0, 'success', $this->db->insert_id());
	}
	
	public function delete_evidence()
	{
		$id = $this->format_get('evidence_id');
		$this->db->query("update `evidence` set is_delete = 1 where id = {$id}");
		$this->output_result(0, 'success', 'success');
	}
	
	function upload_lawyer_image()
	{
		$jsondata = json_decode($this->format_post('data'));
		$user_id = $this->encrypt->decode($jsondata->user_id,$this->key);
		$config['file_name'] = 'lawyer_' . random_string() . '-' . $user_id;
		
		$image_type = $jsondata->image_type;
		
		$config['upload_path'] = getcwd().'/uploads/lawyer/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ( ! $this->upload->do_upload("file"))
		{
			$data['log'] = $this->upload->display_errors();
			$data['create_time'] = time();
			$this->db->insert('log',$data);
			$this->output_result(-1, 'failed', $this->upload->display_errors());
		}
		else
		{
			$path = '/uploads/lawyer/' . $this->upload->data()['file_name'];
		}
		if($image_type == 1)
		{
			$this->db->query("update `lawyer` set certificate_image='{$path}',status='checking' where userid='{$user_id}'");
		}else{
			$this->db->query("update `lawyer` set entrust_image='{$path}',status='checking' where userid='{$user_id}'");
		}
		$this->output_result(0, 'success','success');
	}
	
	public function get_lawyer_image()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'), $this->key);
		$query = $this->db->query("select * from `lawyer` where userid='{$user_id}'");
		$this->output_result(0, 'success',$query->result_array());
	}

	public function get_cases()
	{
		$page = addslashes($_GET['page']);
		$number = addslashes($_GET['number']);
		$order = addslashes($_GET['order']);
		$start = ($page-1) * $number;
		$query = $this->db->query("select * from `case`  where is_green='否' and status='接单中' order by {$order} limit {$start},{$number}");
		$this->output_result(0, 'success', $query->result_array());
	}
	
	public function get_cases_by_victim()
	{
			$userid = $this->encrypt->decode($this->format_get('user_id'), $this->key);
			$page = addslashes($_GET['page']);
			$number = addslashes($_GET['number']);
			$start = ($page-1) * $number;
			$query = $this->db->query("select * from `case` where victim_id='{$userid}' order by createtime desc limit {$start},{$number}");
			$this->output_result(0, 'success', $query->result_array());
	}
	
	public function get_cases_by_lawyer()
	{
			$userid = $this->encrypt->decode($this->format_get('user_id'), $this->key);
			$page = addslashes($_GET['page']);
			$number = addslashes($_GET['number']);
			//$status = addslashes($_GET['status']);
			$start = ($page-1) * $number;
			//$query = $this->db->query("select * from `case` where lawyer_id='{$userid}' and status='{$status}' order by createtime desc  limit {$start},{$number}");
			$query = $this->db->query("select * from `case` where lawyer_id='{$userid}' order by createtime desc  limit {$start},{$number}");
			$this->output_result(0, 'success', $query->result_array());
	}
	
	public function select_lawyer_to_case()
	{
		$case_id = addslashes($_GET['case_id']);
		$quotation_id = addslashes($_GET['quotation_id']);
		$quotation = $this->db->query("select * from `quotation` where id={$quotation_id}")->result_array();
		if(count($quotation) > 0)
		{
			$lawyer_id = $quotation[0]['lawyer_id'];
			$expense = $quotation[0]['expense'];
			//事务
			$this->db->trans_start();
			$this->db->query("update `case` (lawyer_id，status) Values ({$lawyer_id},'已接单')  where id = '{$case_id}'");
			if($this->create_payment($case_id, $lawyer_id, $expense))
			{
				$this->db->trans_commit();
				$this->output_result(0, 'success');
			}else{
				$this->db->trans_rollback();
				$this->output_result(-2, 'error, create payment error');
			}
		}else{
			$this->output_result(-1, 'error,quotation_id and lawyer_id does not match');
		}
	}
	
	public function get_quotations()
	{
		$case_id = addslashes($_GET['case_id']);
		$query = $this->db->query("select * from `case` where case_id = '{$case_id}' order by case_id desc");
		$this->output_result(0, 'success', $query->result_array());
	}
	
	private function create_payment($case_id,$lawyer_id,$expense)
	{
		$first_payment_rate = get_first_payment_rate();
		$total = $expense * $first_payment_rate;
		$data = array('case_id'=>$case_id,
					  'user_id' => $lawyer_id,
					  'total' => $total,
				'create_time' => time(),
				'status' => '未付款'			
		);
		$query = $this->db->insert_string('payment', $data);
		if($this->db->insert_id() > 0)
		{
			return true;
		}else{
			return false;
		}
	}
	
	public function get_payments()
	{
		$user_id = $this->encrypt->decode($this->format_get('user_id'),$this->key);
		$page = addslashes($_GET['page']);
		$number = addslashes($_GET['number']);
		$start = ($page-1) * $number;
		$query = $this->db->query("select * from `payment` where user_id={$user_id} order by create_time desc limit {$start},{$number}");
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
