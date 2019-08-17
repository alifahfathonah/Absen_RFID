<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pegawai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');
        $this->load->library('form_validation');
        if ($this->session->userdata('status') != "login") {
            redirect(base_url("pegawai"));
        }
    }

    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $bulan = date('m');
        $tahun = date('Y');
        $nama_pegawai = $this->session->userdata("nama_pegawai");
        $data['profil'] = $this->Pegawai_model->profil($nama_pegawai);
        $data['title'] = 'Dashbord';
        $data['absen'] = $this->Pegawai_model->total_absen($nama_pegawai, $bulan);
        $data['Tahunabsen'] = $this->Pegawai_model->total_absen_tahun($nama_pegawai, $tahun);
        $this->load->view('pegawai/tempelate/header', $data, null);
        $this->load->view('pegawai/index', $data, null);
        $this->load->view('pegawai/tempelate/footer');
    }
    public function profil()
    {
        $nama_pegawai = $this->session->userdata("nama_pegawai");
        $data['profil'] = $this->Pegawai_model->profil($nama_pegawai);
        $data['title'] = 'Profil';
        $this->load->view('Pegawai/tempelate/header', $data, null);
        $this->load->view('Pegawai/profil', $data, null);
        $this->load->view('Pegawai/tempelate/footer');
    }

    public function editprofil($tbl_idpegawai = null)
    {
        $nama_pegawai = $this->session->userdata("nama_pegawai");
        $data['profil'] = $this->Pegawai_model->profil($nama_pegawai);
        $data['title'] = 'Edit Profil';
        $this->load->view('Pegawai/tempelate/header', $data, null);
        $this->load->view('Pegawai/editprofil', $data, null);
        $this->load->view('Pegawai/tempelate/footer');
    }

    function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('pegawai'));
    }
}
