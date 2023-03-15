<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller{
    
    public $_statusOK = 200;
    public $_statusErr = 500;

    public $_OKmessage = 'Success';
    public $_Errmessage = 'Error';

    public $_table_column_array = ['fullname','email','password'];
    public $_table_login_array = ['email','password'];
    public function __construct(){
		parent ::__construct();
        $this->load->library('session');
        $this->load->library('json');
		$this->load->database();
        $this->load->helper('url');
        $this->load->model('Users_model');
	}
    
    // get request
	public function index()
	{
        $data = $this->Users_model->get_all_users();
        if($data != null){
            echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
        }else{
            echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
        }
    }

    public function login()
	{
		$data = $this->check_array_values($_POST,$this->_table_login_array);
		if(isset($data) && !empty($data)){
			echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
		}else{
			$data = $this->Users_model->login($_POST);
			if($data != null){
                echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response(['message'=>'invalid email/password.'],$this->_Errmessage,$this->_statusErr);
            }
		}
    }
    
    // get request
    public function get_by_id($id){
        if(isset($id) && !empty($id)){
            $result = $this->Users_model->get_user_by_id($id);
            if($result != null){
                echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response($this->db->error(),$this->_Errmessage,$this->_statusErr);
            }
        }else{
            echo $this->json->response('please add id into url.',$this->_Errmessage,$this->_statusErr);
        }
    }

    // post request
    public function register(){
        $data = $this->check_array_values($_POST,$this->_table_column_array);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            $result = $this->Users_model->email_exists($_POST['email']);
            if($result != null){
                echo $this->json->response(['message'=>'Email Already Register in Database'],$this->_Errmessage,$this->_statusErr);
            }else{
                $result = $this->Users_model->save_users($_POST);
                if($result != null){
                    $id = $this->db->insert_id();
                    $data = $this->Users_model->get_user_by_id($id);
                    echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
                }else{
                    echo $this->json->response(['error'=>'Something Went Wrong.'],$this->_Errmessage,$this->_statusErr);
                }
            }
        }
    }

    public function edit_profile(){
        $this->_table_column_array = ['userid','first_name','last_name','email','phone','address','user_avtar'];
        $data = $this->check_array_values($_POST,$this->_table_column_array);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            $result = $this->Users_model->edit_users($_POST,$_POST['userid']);
            
            if($result != null){
                echo $this->json->response($result,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response(['error'=>'something went wrong.'],$this->_Errmessage,$this->_statusErr);
            }
        }
    }


    public function check_array_values($array,$table_array){
        if(isset($array) && !empty($array)){
            $keys = [];
            foreach($array as $key => $value){
                array_push($keys,$key);
            }
            $data = array_diff($table_array,$keys);
            if(isset($data) && !empty($data)){
                $result = [ 
                    'Error_message' => "your post request mising some data.",
                    'Missing_data' => array_values($data)
                ];
                return $result;
            }else{
                return [];
            }
        }else{
            $result = [
                'Error_message' => "your post request is empty.",
                'Missing_data' => $table_array
            ];
            return $result;
        }
    }


    public function upload_file(){
        $this->_table_column_array = ['img','type'];
        $data = $this->check_array_values($_POST,$this->_table_column_array);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            define('UPLOAD_DIR', 'uploads/');
            $img = $_POST['img'];
            $img = str_replace('data:image/'.$_POST['type'].';base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $file = UPLOAD_DIR . uniqid() . '.'.$_POST['type'];
            $success = file_put_contents($file, $data);
            $data = $success ? $file : null;
            if($data != null){
                echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response(['message'=>'Something wrong with your base64.'],$this->_Errmessage,$this->_statusErr);
            }
        }
    }



    public function update_password(){
        $this->_table_column_array = ['new_password','old_password','id'];
        $data = $this->check_array_values($_POST,$this->_table_column_array);
        if(isset($data) && !empty($data)){
            echo $this->json->response($data,$this->_Errmessage,$this->_statusErr);
        }else{
            $data = $this->Users_model->update_password($_POST,$_POST['id']);
            if($data != null){
                echo $this->json->response($data,$this->_OKmessage,$this->_statusOK);
            }else{
                echo $this->json->response(['message'=>'Old password does not match with database.'],$this->_Errmessage,$this->_statusErr);
            }
        }
    }
    
 
}
