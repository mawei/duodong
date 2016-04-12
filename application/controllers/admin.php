<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

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
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('parser');
		$this->load->library('grocery_CRUD');
	}
		
	public function User()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('user');
		$crud->columns('id','username','type');	
		$crud->display_as('username','用户名');
		$crud->display_as('type','类型');
		$crud->display_as('create_time','创建时间');
		$crud->set_subject('用户');
		$output = $crud->render();	
		$this->load->view('UserManagement.php',$output);
	}
	
	public function lawyer()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('lawyer');
		$crud->set_subject('律师');
		$crud->columns('id','userid','name','city','certificate_id','status');
		$crud->display_as('certificate_image','律师执照');
		$crud->display_as('entrust_image','委托书');
		$crud->display_as('name','姓名');
		$crud->display_as('city','城市');
		$crud->display_as('certificate_id','律师执照号');
		$crud->display_as('office','所在律所');
		$crud->display_as('status','状态');
		$crud->set_field_upload('certificate_image','uploads/lawyer');
		$crud->set_field_upload('entrust_image','uploads/lawyer');
		
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function victim()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('victim');
 		$crud->columns('userid','name');
 		//$crud->unset_add();
 		$crud->set_subject('当事人');
		$crud->display_as('name','姓名');
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function quotation()
	{
		$crud = new grocery_CRUD();
		//$crud->set_theme('twitter-bootstrap');
		$crud->set_table('quotation');
		$crud->set_subject('报价');
		$crud->display_as('case_id','案件');
		$crud->display_as('lawyer_id','律师名称');
		$crud->display_as('expense','佣金');
		$crud->display_as('quantity','数量');
		$crud->display_as('compensation','最低赔付额');
		$crud->display_as('status','状态');
		$crud->display_as('create_time','报价时间');
		$crud->set_relation('case_id','case','id');
		$crud->set_relation('lawyer_id','lawyer','name');
		
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function config()
	{
		$crud = new grocery_CRUD();
		//$crud->set_theme('twitter-bootstrap');
		$crud->set_table('config');
		//$crud->columns('id','userid','itemname','quantity','price','total','shop','date');
		//$crud->edit_fields('partner_id','name','sex','age','photo','type');
		$crud->set_subject('网站设置');
		$crud->display_as('key','字段');
		$crud->display_as('alias','名称');
		$crud->display_as('value','内容');
	
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function payment()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('payment');
		$crud->set_subject('付款');
		$crud->display_as('case_id','案件');
		$crud->display_as('user_id','律师');
		$crud->display_as('total','金额');
		$crud->display_as('create_time','创建时间');
		$crud->display_as('status','状态');
		$crud->set_relation('user_id','user','id');
		
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function message()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('message');
		$crud->set_subject('消息');
		$crud->display_as('user_id','用户id');
		$crud->display_as('content','内容');
		$crud->display_as('create_time','创建时间');
		$crud->set_relation('user_id','user','id');
	
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function select_case_to_lawyer()
	{
		$cases = $this->db->query("select id from `case` where status='接单中'")->result_array();
			$lawyers = $this->db->query("select userid from lawyer where status='通过'")->result_array();
			$data['cases'] = $cases;
			$data['lawyers'] = $lawyers;
			
		if(isset($_POST['caseid']))
		{
			$caseid = $_POST['caseid'];
			$lawyerid = $_POST['lawyerid'];
			$case = $this->db->query("select * from `case` where id={$caseid}")->result_array()[0];
			$this->db->query("update `case` set status='已接单',lawyer_id='{$lawyerid}' where id = {$caseid}");
			$this->create_payment($caseid, $lawyerid, 10000);
			$message_lawyer['user_id'] = $lawyerid;
			$message_lawyer['create_time'] = date("Y-M-d H:i:s",time());
			$message_lawyer['content'] = "已为您分配一单案件，请查看看见后进行付款";
			$this->db->insert('message',$message_lawyer);
			$message_victim['user_id'] = $case['victim_id'];
			$message_victim['create_time'] = date("Y-M-d H:i:s",time());
			$message_victim['content'] = "您的案件已分配给律师，请等待律师联系";
			$this->db->insert('message',$message_victim);
			$this->parser->parse('select_case_to_lawyer.php',$data);
		}else{
			$this->parser->parse('select_case_to_lawyer.php',$data);
		}
	}
	
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
	
	private function create_payment($case_id,$lawyer_id,$expense)
	{
		//$first_payment_rate = $this->get_first_payment_rate();
		//$total = $expense * $first_payment_rate;
		$data['case_id'] = $case_id;
		$data['user_id'] = $lawyer_id;
		$data['total'] = $expense;
		$data['status'] = '未付款';
		$data['create_time'] = date("Y-m-d H:i:s",time());
	 	$this->db->insert('payment', $data);
		if($this->db->insert_id() > 0)
		{
			return true;
		}else{
			return false;
		}
	}
	
	public function evidence()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('evidence');
		$crud->set_subject('证据');
		$crud->display_as('case_id','案件');
		$crud->display_as('evidence_image','证据');
		$crud->set_field_upload('evidence_image','assets/uploads/files');
		
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function news()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('news');
		$crud->set_subject('新闻');
		$crud->display_as('title','标题');
		$crud->display_as('content','内容');
		$crud->display_as('category_id','分类');
		$crud->display_as('create_date','创建日期');
		$crud->set_relation('category_id','category','name');
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function newscategory()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('category');
		$crud->set_subject('新闻类别');
		$crud->display_as('name','类别名称');
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	public function cases()
	{
		$crud = new grocery_CRUD();
		$crud->set_table('case');
		$crud->set_subject('案件');
		$crud->display_as('victim_id','受害者');
		$crud->display_as('time','事发时间');
		$crud->display_as('address','地址');
		$crud->display_as('hurt','人身伤害');
		$crud->display_as('lawyer_id','分配律师');
		$crud->display_as('status','状态');
		$crud->display_as('is_green','是否为绿色通道');
		$crud->set_field_upload('file','uploads/image');
		
// 		$crud->set_relation('victim_id','user','username');
// 		$crud->set_relation('lawyer_id','lawyer','userid');
		
		
		$output = $crud->render();
		$this->load->view('UserManagement.php',$output);
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */