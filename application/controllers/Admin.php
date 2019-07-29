<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin_model');
        if ($this->session->userdata('status') != "login") {
            redirect(base_url("login"));
        }
    }

    public function index()
    {
        $username = $this->session->userdata("username");
        $data['profil'] = $this->Admin_model->profil($username);
        $data['title'] = 'Dashbord';
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d');
        $data['grafik'] = $this->Admin_model->grafik();
        $data['absen_hari_ini'] = $this->Admin_model->absen_hari_ini($tanggal);
        $data['total_pegawai'] = $this->Admin_model->total_pegawai();
        $this->load->view('admin/tempelate/header', $data, NULL);
        $this->load->view('admin/dashbord', $data, NULL);
        $this->load->view('admin/tempelate/footer');
    }

    public function pegawai()
    {
        $username = $this->session->userdata("username");
        $data['profil'] = $this->Admin_model->profil($username);
        $data['title'] = 'Data Pegawai';
        $data['pegawai'] = $this->Admin_model->TblPegawai();
        $this->load->view('admin/tempelate/header', $data, null);
        $this->load->view('admin/pegawai', $data, null);
        $this->load->view('admin/tempelate/footer');
    }

    public function inputdatapegawai()
    {
        $username = $this->session->userdata("username");
        $data['profil'] = $this->Admin_model->profil($username);
        $data['title'] = 'Input Data Pegawai';
        $this->load->view('admin/tempelate/header', $data, null);
        $this->load->view('admin/inputpegawai');
        $this->load->view('admin/tempelate/footer');
    }

    public function newpegawai()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'required');
        $this->form_validation->set_rules('tgl', 'Tanggal Lahir', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('jeniskelamin', 'Jenis Kelamin', 'required');
        $this->form_validation->set_rules('agama', 'Agama', 'required');
        $this->form_validation->set_rules('nip', 'Nomor Pegawai', 'required');
        $this->form_validation->set_rules('rfid', 'Nomor Kartu', 'required');
        $this->form_validation->set_rules('kontak', 'Kontak', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');

        date_default_timezone_set('Asia/Jakarta');
        $DTM = date('Y-m-d H:i:s');

        if ($this->form_validation->run() == false) {
            $this->inputdatapegawai();
        } else {
            $nama = ucwords(strtolower($this->security->xss_clean($this->input->post('nama'))));
            $tempat_lahir = ucwords(strtolower($this->security->xss_clean($this->input->post('tempat_lahir'))));
            $tgl = ucwords(strtolower($this->security->xss_clean($this->input->post('tgl'))));
            $alamat = ucwords(strtolower($this->security->xss_clean($this->input->post('alamat'))));
            $jeniskelamin = ucwords(strtolower($this->security->xss_clean($this->input->post('jeniskelamin'))));
            $agama = ucwords(strtolower($this->security->xss_clean($this->input->post('agama'))));
            $nip = ucwords(strtolower($this->security->xss_clean($this->input->post('nip'))));
            $rfid = ucwords(strtolower($this->security->xss_clean($this->input->post('rfid'))));
            $kontak = ucwords(strtolower($this->security->xss_clean($this->input->post('kontak'))));
            $email = ucwords(strtolower($this->security->xss_clean($this->input->post('email'))));
            $foto = base_url() . 'assets/images/fotopegawai/default.png';

            $cek = $this->Admin_model->ceknamapegawai($nama);
            if ($cek > 0) {
                $this->session->set_flashdata('error', 'Nama ada yang sama');
                redirect('Admin/inputdatapegawai');
            } else {
                $ceknip = $this->Admin_model->ceknippegawai($nip);
                if ($ceknip > 0) {
                    $this->session->set_flashdata('error', 'Nomor Pegawai ada yang sama');
                    redirect('Admin/inputdatapegawai');
                } else {
                    $cekrfid = $this->Admin_model->cekrfidpegawai($rfid);
                    if ($cekrfid > 0) {
                        $this->session->set_flashdata('error', 'Nomor Kartu ada yang sama');
                        redirect('Admin/inputdatapegawai');
                    } else {
                        $data = array(
                            'nama_pegawai' => $nama,
                            'tanggal_lahir_pegawai' => $tgl,
                            'tempat_lahir_pegawai' => $tempat_lahir,
                            'nomor_hp_pegawai' => $kontak,
                            'nomor_pegawai' => $nip,
                            'koderfid' => $rfid,
                            'foto' => $foto,
                            'password' =>  getHashedPassword('12345'),
                            'alamat' => $alamat,
                            'jeniskelamin' => $jeniskelamin,
                            'agama' => $agama,
                            'email' => $email,
                            'createDtm' => $DTM
                        );

                        $result = $this->Admin_model->uploaddata($data);
                        if ($result > 0) {
                            $this->session->set_flashdata('success', 'Data Berhasil di Tambah');
                        } else {
                            $this->session->set_flashdata('error', 'Gagal upload data');
                        }
                    }
                }
            }
            redirect('Admin/inputdatapegawai');
        }
    }

    public function daftarhadir()
    {
        $username = $this->session->userdata("username");
        $data['profil'] = $this->Admin_model->profil($username);
        $data['title'] = 'Data Absensi';
        $data['daftarhadir'] = $this->Admin_model->daftarhadir();
        $this->load->view('admin/tempelate/header', $data, null);
        $this->load->view('admin/daftarhadir', $data, null);
        $this->load->view('admin/tempelate/footer');
    }

    public function logadmin()
    {
        $username = $this->session->userdata("username");
        $data['profil'] = $this->Admin_model->profil($username);
        $data['logadmin'] = $this->Admin_model->logadmin();
        $data['title'] = 'Data Log';
        $this->load->view('admin/tempelate/header', $data, null);
        $this->load->view('admin/logadmin', $data, null);
        $this->load->view('admin/tempelate/footer');
    }
    public function profiladmin()
    {
        $username = $this->session->userdata("username");
        $data['profil'] = $this->Admin_model->profil($username);
        $data['title'] = 'Profil';
        $this->load->view('admin/tempelate/header', $data, null);
        $this->load->view('admin/profiladmin', $data, null);
        $this->load->view('admin/tempelate/footer');
    }
    public function editprofil($tbl_idadmin = null)
    {
        $username = $this->session->userdata("username");
        $data['profil'] = $this->Admin_model->profil($username);
        $data['title'] = 'Profil';
        $data['editprofil'] = $this->Admin_model->getprofilbyid($tbl_idadmin);
        $this->load->view('admin/tempelate/header', $data, null);
        $this->load->view('admin/editprofil', $data, null);
        $this->load->view('admin/tempelate/footer');
    }
    public function updateprofil(){
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('kontak', 'Kontak', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('tgl', 'Tanggal lahir', 'required');
        $this->form_validation->set_rules('tmp', 'Tempat Lahir', 'required');

        date_default_timezone_set('Asia/Jakarta');
        $DTM = date('Y-m-d H:i:s');

        if ($this->form_validation->run() == false) {
            $this->editprofil();
        } else {
            $id = ucwords(strtolower($this->security->xss_clean($this->input->post('id'))));
            $nama = ucwords(strtolower($this->security->xss_clean($this->input->post('nama'))));
            $username = ucwords(strtolower($this->security->xss_clean($this->input->post('username'))));
            $email = ucwords(strtolower($this->security->xss_clean($this->input->post('email'))));
            $kontak = ucwords(strtolower($this->security->xss_clean($this->input->post('kontak'))));
            $alamat = ucwords(strtolower($this->security->xss_clean($this->input->post('alamat'))));
            $tgl = ucwords(strtolower($this->security->xss_clean($this->input->post('tgl'))));
            $tmp = ucwords(strtolower($this->security->xss_clean($this->input->post('tmp'))));

            $data= array(
                'nama_admin' => $nama,
                'username_admin' => $username,
                'email_admin' => $email,
                'alamat' => $alamat,
                'nomor_hp' => $kontak,
                'tanggal_lahir' => $tgl,
                'tempat_lahir' => $tmp
            );
            
            $result= $this->Admin_model->updateprofil($data,$id);
            if ($result == true) {
                $this->session->set_flashdata('success', 'Data berhasil di Update');
            } else {
                $this->session->set_flashdata('error', 'Data gagal di update');
            }
            redirect('profiladmin');
        }
    }

    function logout()
    {
        $botToken = "972979337:AAGQ5o0QZ1TgL-CzbOYqJrDE6GGU_cJv5ks";
        $perangkat = $_SERVER['HTTP_USER_AGENT'];
        date_default_timezone_set('Asia/Jakarta');
        $waktu = date('Y-m-d H:i:s');
        $website = "https://api.telegram.org/bot" . $botToken;
        $chatId = -304126311;
        $params = [
            'chat_id' => $chatId,
            'text' => 'User sudah Logout dengan ' . ' ____IP=>' . $_SERVER['REMOTE_ADDR'] . ' ____Tanggal&Jam=>' . $waktu . ' ____Perangkat Lunak =>' . $perangkat,
        ];
        $ch = curl_init($website . '/sendMessage');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $this->session->sess_destroy();

        redirect(base_url('login'));
    }
}
