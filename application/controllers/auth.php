<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class auth extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    public function index()
    {   
        $this->form_validation->set_rules('email','Email','trim|required|valid_email');
        $this->form_validation->set_rules('password','Password','trim|required');
        if($this->form_validation->run() == false){
        $this->load->view('templates/auth_header');
        $this->load->view('auth/login');
        $this->load->view('templates/auth_footer');
        } else {
            //validation success
            $this->_login();
        }
        
    }

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])-> row_array();

        if($user){

            if($user['is_active'] == 1){
            if(password_verify($password, $user['password'])) {
                $data =[
                    'email' => $user['email'],
                    'role' => $user['role']

                ];
                $this->session->set_userdata($data);
                redirect('user');
            }else{
                $this->session->set_flashdata('message','<div class ="alert alert-danger" role="alert">Login Failed</div>');

            }
            } else {
                $this->session->set_flashdata('message','<div class ="alert alert-danger" role="alert">Login Failed</div>');

            }
        } else {
            $this->session->set_flashdata('message','<div class ="alert alert-danger" role="alert">Login Failed</div>');

        }
    }

    public function registration()
    {
        $this->form_validation->set_rules('name','Name','required|trim');
        $this->form_validation->set_rules('email','Email','required|trim|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('password1','Password','required|trim|min_length[6]|matches[password2]');
        $this->form_validation->set_rules('password2','Password','required|trim|min_length[6]|matches[password1]');
        
        if(

        $this->form_validation->run() == false){
        $this->load->view('templates/auth_header');
        $this->load->view('auth/registration');
        $this->load->view('templates/auth_footer');

        } else {
            $data = [
                'name'=> htmlspecialchars($this->input->post('name',true)),
                'email'=> htmlspecialchars($this->input->post('email',true)),
                'image'=> 'https://setda.majalengkakab.go.id/an-component/media/upload-user-avatar/default.jpg',
                'password'=> password_hash($this->input->post('password1'),
                PASSWORD_DEFAULT),
                'role' => 2,
                'is_active' => 1,
                'date_created' => date("Y-m-d H:i:s"),
            ];

            $this->db->insert('user',$data);
            redirect('auth');

        }

    }
}