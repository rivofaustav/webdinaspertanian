<?php defined('BASEPATH') OR exit('No direct script access allowed');

class galeri_model extends CI_Model
{
    private $_table = "galeri";

    public $id_galeri;
    public $id_ktbidang;
    public $judul;
    public $isi;
    public $gambar;
    public $date;

    public function rules()
    {
        return [
            ['field' => 'judul',
            'label' => 'Judul',
            'rules' => 'required'],

            ['field' => 'isi',
            'label' => 'Isi',
            'rules' => 'required']
        ];
    }

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }
    
    public function getById($id)
    {
        return $this->db->get_where($this->_table, ["id_galeri" => $id])->row();
    }

    public function save()
    {
        $post = $this->input->post(); //ambil data dari form
        // $this->id_pengumuman = uniqid(); // membubat id unik
        $this->judul = $post["judul"]; // isi field judul
        $this->isi = $post["isi"]; // isi field isi

        return $this->db->insert($this->_table, $this); //simpan ke database
    }

    public function update()
    {
        $post = $this->input->post();
        $this->id_galeri = $post["id_galeri"];
        $this->judul = $post["judul"];
        $this->isi = $post["isi"];
       
        return $this->db->update($this->_table, $this, array('id_galeri' => $post['id_galeri']));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id_galeri" => $id));
    }
}