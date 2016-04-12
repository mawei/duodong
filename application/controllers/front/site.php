<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Site extends CI_Controller {

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
		$this->appid = "wx13facdc7a21c75b6";
		$this->secret = "8ceb383fc897603a7edeab04c5311d37";
		
		$head = $this->load->view("front/include/head","",true);
		$header = $this->load->view("front/include/header","",true);
		$footer = $this->load->view("front/include/footer","",true);
		$this->data['head'] = $head;
		$this->data['header'] = $header;
		$this->data['footer'] = $footer;
	}
	
	public function index()
	{
		$this->parser->parse('front/index',$this->data);
	}
	
	public function profile(){
		$this->needLogin();
		$insurances = $this->db->query("select * from insurance where userid={$this->session->userdata('userid')}")->result_array()[0];
		$insurances['image'] = base_url('assets/uploads/files/' . $insurances['image']);
		$insurances['ID_front_image'] = base_url('assets/uploads/files/' . $insurances['ID_front_image']);
		$insurances['ID_back_image'] = base_url('assets/uploads/files/' . $insurances['ID_back_image']);			$insurances[$k]['bank_image'] = base_url('assets/uploads/files/' . $v['bank_image']);
		$this->data['insurances'] = $insurances;
		
		$weihus = $this->db->query("select * from maintenance_record where userid={$this->session->userdata('userid')}");
		$this->data['weihus'] = $weihus->result_array();
		
		$this->parser->parse('front/profile',$this->data);
	}
	
	public function workerlist(){
		$this->needLogin();
		$records = $this->db->query("select * from worker")->result_array();
		foreach ($records as $k=>$v)
		{
			$records[$k]['image'] = base_url('assets/uploads/files/' . $v['image']);
		}
		$this->data['records'] = $records;
		$this->parser->parse('workerlist',$this->data);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */