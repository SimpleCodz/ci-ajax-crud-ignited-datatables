<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barang extends CI_Controller {

	public $validation_for = '';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('datatables');// Load Library Ignited-Datatables
        $this->load->model('barang_model','m_barang');
        $this->load->helper('form');
		$this->load->library('form_validation');
    }

	public function index()
	{
		$this->load->view('view_barang');
	}

	function get_guest_json() { //data data produk by JSON object
		header('Content-Type: application/json');
		echo $this->m_barang->get_all_barang();
	}

	function getKategori(){
		header('Content-Type: application/json');
		echo json_encode($this->m_barang->get_kategori());
	}

	public function edit($id)
	{
		header('Content-Type: application/json');
		echo json_encode($this->m_barang->get_by_id($id));
	}

	public function delete($id)
	{
		$this->db->where('id_barang',$id);
		$this->db->delete('data_barang');
	}

	public function add()
	{
		$this->validation_for = 'add';
        $data = array();
		$data['status'] = TRUE;

		$this->_validate();

        if ($this->form_validation->run() == FALSE)
        {
            $errors = array(
                'nama_barang' 	=> form_error('nama_barang'),
                'kategori' 		=> form_error('kategori'),
                'stok' 		=> form_error('stok'),
			);
            $data = array(
                'status' 		=> FALSE,
				'errors' 		=> $errors
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }else{
            $insert = array(
					'nama_barang'	=> $this->input->post('nama_barang'),
					'kategori_id' 	=> $this->input->post('kategori'),
					'stok' 			=> $this->input->post('stok')
				);
			$this->db->insert('data_barang', $insert);
            $data['status'] = TRUE;
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
	}

	public function update()
	{
		$this->validation_for = 'update';
		$data = array();
		$data['status'] = TRUE;

		$this->_validate();

        if ($this->form_validation->run() == FALSE){
			$errors = array(
                'nama_barang' 	=> form_error('nama_barang'),
                'kategori' 		=> form_error('kategori'),
                'stok' 		=> form_error('stok'),
			);
            $data = array(
                'status' 		=> FALSE,
				'errors' 		=> $errors
            );
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
		}else{
			$update = array(
					'nama_barang'	=> $this->input->post('nama_barang'),
					'kategori_id' 	=> $this->input->post('kategori'),
					'stok' 			=> $this->input->post('stok')
				);
			$this->db->where('id_barang', $this->input->post('id'));
			$this->db->update('data_barang', $update);
			$data['status'] = TRUE;
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
		}
	}

	private function _validate()
	{
		$this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'trim|required|min_length[2]|max_length[30]');
        $this->form_validation->set_rules('kategori', 'Kategori Barang', 'trim|required');
        $this->form_validation->set_rules('stok', 'stok', 'trim|required|numeric');
	}

}