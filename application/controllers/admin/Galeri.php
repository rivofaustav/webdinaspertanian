<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Galeri extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("galeri_model");
        $this->load->library('form_validation');
        
        $this->load->model("user_model");
        if($this->user_model->isNotLogin()) redirect(site_url('admin/login'));
    }

    public function index()
    {
        $data["galeri"] = $this->galeri_model->getAll();
        $this->load->view("admin/galeri/list", $data);
    }

    public function add()
    {
        $galeri = $this->galeri_model; //objek model
        $validation = $this->form_validation; // objek form validation
        $validation->set_rules($galeri->rules()); //terapkan rules

        if ($validation->run()) { //melakukan validasi
            $galeri->save(); //simpan data ke database
            $this->session->set_flashdata('success', 'Berhasil disimpan'); //tampilkan pesan berhasil
        }

        $this->load->view("admin/galeri/new_form"); //tampilkan form add
    }

    public function edit($id = null)
    {
        if (!isset($id)) redirect('admin/galeri');
       
        $galeri = $this->galeri_model;
        $validation = $this->form_validation;
        $validation->set_rules($galeri->rules());

        if ($validation->run()) {
            $galeri->update();
            $this->session->set_flashdata('success', 'Berhasil disimpan');
        }

        $data["galeri"] = $galeri->getById($id);
        if (!$data["galeri"]) show_404();
        
        $this->load->view("admin/galeri/edit_form", $data);
    }

    public function delete($id=null)
    {
        if (!isset($id)) show_404();
        
        if ($this->galeri_model->delete($id)) {
            redirect(site_url('admin/galeri'));
        }
    }
}