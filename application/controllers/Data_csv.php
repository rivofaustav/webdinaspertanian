<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data_csv extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        
        // Load data_csv model
        $this->load->model('data_csv');
        
        // Load form validation library
        $this->load->library('form_validation');
        
        // Load file helper
        $this->load->helper('file');
    }
    
    public function index(){
        $data = array();
        
        // Get messages from the session
        if($this->session->userdata('success_msg')){
            $data['success_msg'] = $this->session->userdata('success_msg');
            $this->session->unset_userdata('success_msg');
        }
        if($this->session->userdata('error_msg')){
            $data['error_msg'] = $this->session->userdata('error_msg');
            $this->session->unset_userdata('error_msg');
        }
        
        // Get rows
        $data['data_csv'] = $this->data_csv->getRows();
        
        // Load the list page view
        $this->load->view('data_csv/index', $data);
    }
    
    public function import(){
        $data = array();
        $memData = array();
        
        // If import request is submitted
        if($this->input->post('importSubmit')){
            // Form field validation rules
            $this->form_validation->set_rules('file', 'CSV file', 'callback_file_check');
            
            // Validate submitted form data
            if($this->form_validation->run() == true){
                $insertCount = $updateCount = $rowCount = $notAddCount = 0;
                
                // If file uploaded
                if(is_uploaded_file($_FILES['file']['tmp_nama'])){
                    // Load CSV reader library
                    $this->load->library('CSVReader');
                    
                    // Parse data from CSV file
                    $csvData = $this->csvreader->parse_csv($_FILES['file']['tmp_nama']);
                    
                    // Insert/update CSV data into database
                    if(!empty($csvData)){
                        foreach($csvData as $row){ $rowCount++;
                            
                            // Prepare data for DB insertion
                            $memData = array(
                                'nama' => $row['Nama'],
                                'desa' => $row['Desa'],
                                'komoditas' => $row['Komoditas'],
                                'status' => $row['Status'],
                            );
                            
                        //     // Check whether desa already exists in the database
                        //     $con = array(
                        //         'where' => array(
                        //             'desa' => $row['Desa']
                        //         ),
                        //         'returnType' => 'count'
                        //     );
                        //     $prevCount = $this->data_csv->getRows($con);
                            
                        //     if($prevCount > 0){
                        //         // Update data_csv data
                        //         $condition = array('desa' => $row['Desa']);
                        //         $update = $this->data_csv->update($memData, $condition);
                                
                        //         if($update){
                        //             $updateCount++;
                        //         }
                        //     }else{
                        //         // Insert data_csv data
                        //         $insert = $this->data_csv->insert($memData);
                                
                        //         if($insert){
                        //             $insertCount++;
                        //         }
                        //     }
                        // }
                        
                        // Status message with imported data count
                        $notAddCount = ($rowCount - ($insertCount + $updateCount));
                        $successMsg = 'data_csv imported successfully. Total Rows ('.$rowCount.') | Inserted ('.$insertCount.') | Updated ('.$updateCount.') | Not Inserted ('.$notAddCount.')';
                        $this->session->set_userdata('success_msg', $successMsg);
                    }
                }else{
                    $this->session->set_userdata('error_msg', 'Error on file upload, please try again.');
                }
            }else{
                $this->session->set_userdata('error_msg', 'Invalid file, please select only CSV file.');
            }
        }
        redirect('data_csv');
    }
    
    /*
     * Callback function to check file value and type during validation
     */
    public function file_check($str){
        $allowed_mime_types = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
        if(isset($_FILES['file']['nama']) && $_FILES['file']['nama'] != ""){
            $mime = get_mime_by_extension($_FILES['file']['nama']);
            $fileAr = explode('.', $_FILES['file']['nama']);
            $ext = end($fileAr);
            if(($ext == 'csv') && in_array($mime, $allowed_mime_types)){
                return true;
            }else{
                $this->form_validation->set_message('file_check', 'Please select only CSV file to upload.');
                return false;
            }
        }else{
            $this->form_validation->set_message('file_check', 'Please select a CSV file to upload.');
            return false;
        }
    }
}