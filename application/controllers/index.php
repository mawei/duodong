<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class index extends CI_Controller {

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
		$this->load->library('parser');
		$this->load->helper('text');
		
		$head = $this->load->view("include/head","",true);
		$header = $this->load->view("include/header","",true);
		$footer = $this->load->view("include/footer","",true);
		$this->data['head'] = $head;
		$this->data['header'] = $header;
		$this->data['footer'] = $footer;
		
		$categorys = $this->db->query("select * from `category`")->result_array();
		$this->data['categorys'] = $categorys;
		
	}
	
	
	public function needlogin()
	{
			if($this->session->userdata('userid') > 0)
			{
				return "success";
			}else{
				redirect("index/login");
			}
	}
	
	
	public function index()
	{
		$this->data['content'] = $this->load->view("index","",true);
		$this->parser->parse('fullpage',$this->data);
	}
	
	public function news($category)
	{
		$page = isset($_GET['page']) ? $_GET['page']:1;
		$count = 10;
		$start = ($page-1)*$count;
		$this->data['content'] = $this->load->view("news","",true);
		$news = $this->db->query("select * from `news` where category_id={$category} order by create_date desc limit $start,$count")->result_array();
		foreach ($news as $k=>$v)
		{
			$news[$k]['content'] = mb_substr(strip_tags($v['content']), 0,300);
		}
		$this->data['news'] = $news;
		$this->data['page'] = $page;
		$this->data['lastpage'] = $page - 1 > 0 ? $page -1 : 1;
		$this->data['nextpage'] = $page + 1;
		$this->data['category'] = $category;
		$this->parser->parse('fullpage',$this->data);
	}
	
	public function newsdetail($id)
	{
		$news = $this->db->query("select * from `news` where id={$id}")->result_array();
		$this->data['content'] = $this->load->view("newsdetail","",true);
		$this->data['title'] = $news[0]['title'];
		$this->data['news_content'] = $news[0]['content'];
		$this->parser->parse('fullpage',$this->data);
	}
	
	public function service()
	{
		$this->data['content'] = $this->load->view("service","",true);
		$this->parser->parse('fullpage',$this->data);
	}
	
	public function login()
	{
		//微信登陆
		$this->data['weixinID'] = isset($_GET['weixinID']) ? $_GET['weixinID'] :"";
		$this->data['referurl'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "index";
		$this->data['error'] = "";
		$this->parser->parse('login',$this->data);
	}
	
	public function  loginout()
	{
		$this->session->set_userdata("userid",0);
		$this->data['error'] = "";
		$this->parser->parse('login',$this->data);
	}
	
	public function loginpost()
	{
		$username = addslashes($_POST['username']);
		$password = addslashes($_POST['password']);
		$query = $this->db->query("select * from `user` where username='{$username}' and password='{$password}'");
		if(count($query->result_array())>0)
		{
			$this->session->set_userdata('userid', $query->result_array()[0]['id']);
			if($weixinID != "")
			{
				$this->db->query("update `user` set weixinID='{$weixinID}' where id = {$query->result_array()[0]['id']}");
			}
			//print_r($referurl);die();
			if($query->result_array()[0]['type'] == 'admin')
			{
				redirect(site_url("admin/user"));
			}else{
				redirect("index");
			}
		}else{
			$error = "请输入正确的用户名及密码";
			$this->data['error'] = $error;
			$this->parser->parse('login',$this->data);
		}
	}
	
	public function register()
	{
		$this->data['error'] = "";
		$this->parser->parse('register',$this->data);
	}
	
	public function registerpost()
	{
		$username = addslashes($_POST['username']);
		$password = addslashes($_POST['password']);
		$password2 = addslashes($_POST['password2']);
		$phone = addslashes($_POST['phone']);
		$carmodel = addslashes($_POST['carmodel']);
		
		if($username == "" || $password==""||$password2==""||$phone==""||$carmodel=="")
		{
			$error = "请输入完整";
			$this->data['error'] = $error;
			$this->parser->parse('register',$this->data);
		}
		elseif($password != $password2)
		{
			$error = "密码不一致";
			$this->data['error'] = $error;
			$this->parser->parse('register',$this->data);
		}
		else{
			$query = $this->db->query("select * from `user` where username='{$username}'");
			if(count($query->result_array())>0)
			{
				$error = "用户名已存在";
				$this->data['error'] = $error;
				$this->parser->parse('register',$this->data);
			}else {
				$query = $this->db->query("insert into `user` (username,password,phone,carmodel) values ('{$username}','{$password}','{$phone}','{$carmodel}')");
				redirect('index/login');
			}
		}
	}
	
	public function complete_profile()
	{
		$this->parser->parse('complete_profile',$this->data);
	}
	
	public function complete_profile_post()
	{
		$wheel = addslashes($_POST['wheel']);
		$carmodel = addslashes($_POST['carmodel']);
		if($wheel == "" || $carmodel=="")
		{
			$error = "请输入完整";
			$this->data['error'] = $error;
			$this->parser->parse('complete_profile',$this->data);
		}
		else
		{
			$query = $this->db->query("select * from `user` where username='{$username}'");
			if(count($query->result_array())>0)
			{
				$error = "用户名已存在";
				$this->data['error'] = $error;
				$this->parser->parse('register',$this->data);
			}else{
				$query = $this->db->query("update `user` (carmodel,wheel) values ('{$carmodel}','{$wheel}')");
				redirect('index/login');
			}
		}
	}
	

	
	public function suggest()
	{
		$message = "";
		$this->data['message'] = $message;
		if(isset($_POST['content']) && $_POST['content'] != "")
		{
			$this->db->query("insert into `suggest` (content,userid) values (\"{$_POST['content']}\",\"{$this->session->userdata('userid')}\")");
			$this->data['message'] = "提交成功";
			$message = "提交成功";
			$this->parser->parse('suggest',$this->data);
		}else{
			$this->parser->parse('suggest',$this->data);
		}
	}
	
	public function joinus()
	{
		$result = $this->db->query("select * from `config` where `key`='joinus'")->result_array();
		if(count($result) > 0)
		{
			$this->data['joinus'] = $result[0]['value'];
		}else{
			$this->data['joinus'] = "暂时没有";
		}
		$this->parser->parse('joinus',$this->data);
	}
	
	public function  findus()
	{
		$result = $this->db->query("select * from `config` where `key`='findus'")->result_array();
		if(count($result) > 0)
		{
			$this->data['findus'] = $result[0]['value'];
		}else{
			$this->data['findus'] = "暂时没有";
		}
		$this->parser->parse('findus',$this->data);
	}
	
	public function  wheel()
	{
		$loginResult = $this->needlogin();
		if($loginResult == 'success')
		{
			$query = $this->db->query("select * from `user` where id={$this->session->userdata('userid')}");
			$this->data['wheel'] = $query->result_array()[0]['wheel'];
			$this->parser->parse('wheel',$this->data);
		}elseif($loginResult != '')
		{
			redirect("index/login?weixinID={$loginResult}");
		}
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */