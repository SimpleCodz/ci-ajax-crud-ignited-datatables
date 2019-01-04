<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barang_model extends CI_Model {
    public function get_kategori(){
        $result = $this->db->get('kategori');
        return $result->result();
    }
    public function get_all_barang() {
        $this->datatables->select('id_barang, nama_barang, stok, id_kategori, kategori_barang');
        $this->datatables->from('data_barang');
        $this->datatables->join('kategori', 'kategori_id=id_kategori');
        $this->datatables->add_column('view', '<button type="button" onclick="edit_barang($1)" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</button>  <button onclick="hapus_barang($1)" type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus</button>','id_barang,nama_barang,stok,id_kategori,kategori_barang');
        return $this->datatables->generate();
    }
    public function get_by_id($id)
	{
		$this->db->from('data_barang');
		$this->db->where('id_barang',$id);
		$query = $this->db->get();
		return $query->row();
	}
}