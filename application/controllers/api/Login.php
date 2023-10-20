<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;

class Login extends REST_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');	
        // $this->load->model('Product_model');
     }

     public function index_get()
     {
        $this->response([
            "status" => true,
            "message" => "Welcome to CodeIgniter RESTful API"
        ], REST_Controller::HTTP_OK); 
     }
}