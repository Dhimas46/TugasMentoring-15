<?php
defined('BASEPATH') OR exit('No direct script access allowed');
   require APPPATH . '/libraries/REST_Controller.php';
   use Restserver\Libraries\REST_Controller;
     
class Products extends REST_Controller {

    /**
     * __construct function.
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model('M_Products');
    }
    
    /**
     * index function.
     * 
     * @access public
     * @return void
     */

     public function index_get($id = 0)
     {
         // Mendapatkan token dari header permintaan
         $headers = $this->input->request_headers();
         
         if (isset($headers['Authorization'])) {
             $token = $headers['Authorization'];
             
             // Memeriksa validitas token JWT
             $decodedToken = $this->authorization_token->validateToken($token);
             
             if ($decodedToken['status']) {
                 // Token JWT valid, izinkan akses ke data produk dengan menggabungkan tabel M_Products dan M_Category
                 $this->db->select('products.*, category.category_name');
                 $this->db->from('products');
                 $this->db->join('category', 'products.category_id = category.category_id', 'left');
                 $data = $this->db->get()->result();
     
                 $this->response([
                     "status" => true,
                     "message" => "Product List with Categories",
                     "data" => $data
                 ], REST_Controller::HTTP_OK);
             } else {
                 // Token JWT tidak valid, beri respons kesalahan
                 $this->response([
                     "status" => false,
                     "message" => "Token JWT tidak valid"
                 ], REST_Controller::HTTP_UNAUTHORIZED);
             }
         } else {
             // Header Authorization tidak ditemukan, beri respons kesalahan
             $this->response([
                 "status" => false,
                 "message" => "Token JWT tidak ditemukan"
             ], REST_Controller::HTTP_UNAUTHORIZED);
         }
     }
     
    
    
    /**
     * show function.
     * 
     * @access public
     * @param mixed $id (default: 0)
     * @return void
     */
    public function show_get($id = 0)
    {
        $data = $this->Product_model->show($id);
        $this->response([
            "status" => true,
            "message" => "Product List",
            "data" => $data
        ], REST_Controller::HTTP_OK); 
    }
    
    /**
     * insert function.
     * 
     * @access public
     * @return void
     */
    public function index_post()
    {
        $data = array(
            'product_name' => $this->post('product_name'),
            'category_id' => $this->post('category_id'),
            'price' => $this->post('price'),
            'quantity' => $this->post('quantity'),
            'description' => $this->post('description'),
            'created_at' => date('Y-m-d H:i:s')
        );
        $insert = $this->M_Products->insert($data);
        if($insert){
            $this->response([
                "status" => true,
                "message" => "Product added successfully"
            ], REST_Controller::HTTP_OK); 
        }else{
            $this->response([
                "status" => false,
                "message" => "Product not added"
            ], REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    
    /**
     * update function.
     * 
     * @access public
     * @param mixed $id
     * @return void
     */
    public function index_put($id)
    {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if ($decodedToken['status']) {
                // ------- Main Logic part -------
                // $input = $this->put();
                $headers = $this->input->request_headers();
                $data['product_name'] = $headers['product_name'];
                $data['category_id  '] = $headers['category_id'];
                $data['price'] = $headers['price'];
                $data['quantity'] = $headers['quantity'];
                $data['description'] = $headers['description'];
                $response = $this->M_Products->update($data, $id);
                $product = $this->M_Products->show($id);

                $response > 0
                    ? $this->response([
                        'message' => 'Product updated successfully.',
                        'data' => $product
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
                $response = $this->M_Products->delete($id);

                $response>0?$this->response(['Product deleted successfully.'], REST_Controller::HTTP_OK):$this->response(['Not deleted'], REST_Controller::HTTP_OK);
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