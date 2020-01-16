<?php

class Admin_model extends CI_Model
{
    private $_table = "admin";

    public function doLogin(){
		$post = $this->input->post();

        // cari user berdasarkan email dan username
        $this->db->where('username', $post["username"]);
        $user = $this->db->get($this->_table)->row();

        // jika user terdaftar
        if($user){
            // periksa password-nya
            $isPasswordTrue = password_verify($post["password"], $user->password);


            // jika password benar dan dia admin
            if($isPasswordTrue){ 
                // login sukses yay!
                $this->session->set_userdata(['user_logged' => $user]);

                return true;
            }
        }
        
        // login gagal
		return false;
    }

    public function isNotLogin(){
        return $this->session->userdata('user_logged') === null;
    }


}