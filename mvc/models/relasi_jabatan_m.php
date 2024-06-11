<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Relasi_jabatan_m  extends MY_Model {

	protected $_table_name = 'relasi_jabatan';
	protected $_primary_key = 'relasi_jabatan.Id';
	protected $_primary_filter = 'intval';
	protected $_order_by = "roll asc";

	public function __construct() 
	{
		parent::__construct();
	}

	public function insert_relasi_jabatan($data) {
        // Ensure $data is an array of arrays
        if (!empty($data) && is_array($data[0])) {
            // Insert batch data
            $this->db->insert_batch('relasi_jabatan', $data);
            // Check if rows were affected
            return $this->db->affected_rows() > 0;
        } else {
            log_message('error', 'Invalid data format for insert_batch: ' . print_r($data, true));
            return false;
        }
    }
}