<?php
require_once APPPATH.'/core/Main_model.php';
class Users_model extends Main_model
{
    public $table_name = "users";
	public function __construct(){
		parent::__construct();
        $this->load->library('upload','encrypt');
        $this->load->library('encryption');
        $this->load->helper('string');
        
    }

    public function login($user_data){
        $where = "email = '".$user_data['email']."' ";
        $password = $this->get_by($this->table_name,$where,'password','row');
        if($password != null){
            if(password_verify($user_data['password'],$password->password)){
                $data = $this->get($this->table_name,$where);
                return $data;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }


    public function get_all_users(){
        $data = $this->get($this->table_name);
        return $data;
    }

    public function get_user_by_id($id){
        $where = 'id = '.$id;
        $data = $this->get($this->table_name,$where);
        return $data;
    }

    public function save_users($data){
        $values = [
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'password' => password_hash($data['password'],PASSWORD_BCRYPT),
        ];
        return $this->insert($this->table_name,$values);
    }

    public function edit_users($data,$id){
        $values = [
            'email' => $data['email'],
            'fullname' => $data['fullname'],
        ];
        $where = "id = ".$id;
        return $this->update($this->table_name,$values,$where);
    }

    public function update_password($data,$id){
        $where = 'id ='.$id;
        $user = $this->get($this->table_name,$where);
        if(password_verify($data['old_password'],$user->password)){
            $values = [
                'password' => password_hash($data['new_password'],PASSWORD_BCRYPT)
            ];
            $where = "userid=".$id;
            return $this->update($this->table_name,$values,$where);
        }else{
            return null;
        }
    }

    public function delete_users($id){
        $where = "id =".$id;
        return $this->delete($this->table_name,$where);
    }

    public function email_exists($email){
        $where = 'email ="'.$email.'"';
        $data = $this->get($this->table_name,$where);
        if($data != null){
            return true;
        }else{
            return false;
        }
    }

    public function upload_user_file($file){
        return $this->upload_file($file);
    }
    
}