<?php
defined('BASEPATH') OR exit('No direct script access allowed');
   require APPPATH . '/libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Category extends REST_Controller {
 
    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('M_Category');
    }

    public function index_get($id = 0){
        $headers = $this->input->request_headers(); 
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

            if ($decodedToken['status'])
            {
                // ------- Main Logic part -------
                if(!empty($id)){
                    $data = $this->M_Category->show($id);
                } else {
                    $data = $this->M_Category->show();
                }
                $this->response($data, REST_Controller::HTTP_OK);
                // ------------- End -------------
            } 
            else {
                $this->response($decodedToken);
            }
        } else {
            $this->response(['Authentication failed'], REST_Controller::HTTP_OK);
        }
    }

    public function index_post(){
        $headers = $this->input->request_headers(); 
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);

            if ($decodedToken['status'])
            {
                // ------- Main Logic part -------
                $input = $this->input->post();
                $data = $this->M_Category->insert($input);
                $this->response($data, REST_Controller::HTTP_OK);
                // ------------- End -------------
            } 
            else {
                $this->response($decodedToken);
            }
        } else {
            $this->response(['Authentication failed'], REST_Controller::HTTP_OK);
        }
    }

    public function index_put($id)
    {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status']) {
                // ------- Main Logic part -------
                // $input = $this->put();
                $headers = $this->input->request_headers();
                $data['category_name'] = $headers['category_name'];
                $response = $this->M_Category->update($data, $id);
                $category = $this->M_Category->show($id);

                $response > 0
                    ? $this->response([
                        'message' => 'Category updated successfully.',
                        'data' => $category
                    ], REST_Controller::HTTP_OK)
                    : $this->response([
                        'message' => 'Not updated',
                        'errors' => [],
                    ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                // ------------- End -------------
            } else {
                $this->response([
                    'message' => 'Authentication failed'
                ], REST_Controller::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response([
                'message' => 'Authentication failed'
            ], REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function index_delete($id)
    {
        
        $headers = $this->input->request_headers(); 
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status'])
            {
                // ------- Main Logic part -------
                $response = $this->M_Category->delete($id);

                $response>0?$this->response(['Category deleted successfully.'], REST_Controller::HTTP_OK):$this->response(['Not deleted'], REST_Controller::HTTP_OK);
                // ------------- End -------------
            }
            else {
                $this->response($decodedToken);
            }
        }
        else {
            $this->response(['Authentication failed'], REST_Controller::HTTP_OK);
        }
    }
}