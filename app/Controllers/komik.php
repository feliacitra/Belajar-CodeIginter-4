<?php

namespace App\Controllers;

use App\Models\KomikModel;

class komik extends BaseController
{
    protected $KomikModel;

    public function __construct()
    {
        $this->KomikModel = new KomikModel();
        $this->email = \Config\Services::email();
    }
    public function index()
    {
        //$komik = $this->KomikModel->findAll();

        $data = [
            'title' => 'Daftar Komik | web saya',
            'komik' => $this->KomikModel->getKomik()
        ];

        //cara konek db tanpa modedl
        // $db = \Config\Database::connect();
        // $komik = $db->query("SELECT * FROM komik");
        // dd($komik);
        // foreach ($komik->getResultArray() as $row) {
        //     d($row);
        // }
        return view('komik/index', $data);
    }

    public function detail($slug)
    {

        $data = [
            'title' => 'Detail Komik | web saya',
            'komik' => $this->KomikModel->getKomik($slug)
        ];

        //jika komik tidak ada di tabel
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Komik' . $slug . 'tidak ditemukan');
        }
        return view('komik/detail', $data);
    }

    public function create()
    {
        //session();
        $data = [
            //halaman atas search web
            'title' => 'Form Tambah Data Komik',
            'validation' => \Config\Services::validation()
        ];
        return view('komik/create', $data);
    }

    public function save()
    {

        //validasi input
        if (!$this->validate([
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => '{field} komik harus diisi.',
                    'is_unique' => '{field} Komik sudah ada'
                ]
            ],
            //biar gambarnya disi berdsarkn ukuran max 1 mb
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jgp,image/jpeg,image/png]',
                // cara nampilin pesan error karena ukuran tak ssuai
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]

        ])) {
            // $validation = \Config\Services::validation();
            // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
            return redirect()->to('/komik/create')->withInput();
        }

        //ambil gambar
        $fileSampul = $this->request->getFile('sampul');
        //biar kalo input gambar kosong yg kesimpan default gambar
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'default.jpg';
        } else {
            //generate nama sampul random
            $namaSampul = $fileSampul->getRandomName();
            //pindahkan file ke folder img
            $fileSampul->move('img', $namaSampul);
            //ambil nama file
            //$namaSampul = $fileSampul->getName();
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->KomikModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data Berhasil Ditambah');

        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        //cari gambar berdasarkan id
        $komik = $this->KomikModel->find($id);

        //cek jika file gambarnya default.jpg
        if ($komik['sampul'] != 'default.jpg') {
            //hapus gambar
            unlink('img/' . $komik['sampul']);
        }


        $this->KomikModel->delete($id);
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus');
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        $data = [
            //halaman atas search web
            'title' => 'Form Ubah Data Komik',
            'validation' => \Config\Services::validation(),
            'komik' => $this->KomikModel->getKomik($slug)
        ];
        return view('komik/edit', $data);
    }

    public function update($id)
    {
        //cek judul
        $komikLama = $this->KomikModel->getKomik($this->request->getVar('slug'));
        if ($komikLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }
        //validasi input biar disi
        if (!$this->validate([
            'judul' => [
                'rules' => $rule_judul,
                'errors' => [
                    'required' => '{field} komik harus diisi.',
                    'is_unique' => '{field} Komik sudah ada'
                ]
            ],
            'sampul' => [
                'rules' => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jgp,image/jpeg,image/png]',
                // cara nampilin pesan error karena ukuran tak ssuai
                'errors' => [
                    'max_size' => 'Ukuran gambar terlalu besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {

            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
        }


        $fileSampul = $this->request->getFile('sampul');

        //cek gambar apakah tetap gambar lama
        if ($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            //generate nama file random
            $namaSampul = $fileSampul->getRandomName();
            //pindahkan gambar
            $fileSampul->move('img', $namaSampul);
            //hapus file yang lama
            unlink('img/' . $this->request->getVar('sampulLama'));
        }
        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->KomikModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data Berhasil Diubah');

        return redirect()->to('/komik');
    }

    public function sendEmail()
    {
        $this->email->setFrom('feliacitra28@gmail.com', 'Felia Citra');
        $this->email->setTo('17102128@ittelkom-pwt.ac.id');

        $this->email->setSubject('Testing Email Ci4');
        $this->email->setMessage('<h1>Pesan Darurat</h1> <p>Hallo Felia Ini Felia</p>');

        if (!$this->email->send()) {
            return false;
        } else {
            return redirect()->to('/komik');
        }
        // if ($this->email->send() == true) {
        // }
    }

    public function kirim()
    {
        $e = $this->request->getVar('email');
        $s = $this->request->getVar('subject');
        $m = $this->request->getVar('message');

        $this->email->setFrom('feliacitra28@gmail.com', 'Felia Citra');
        $this->email->setTo($e);
        $this->email->setSubject($s);
        $this->email->setMessage($m);

        if (!$this->email->send()) {
            return false;
        } else {
            echo "email berhasil di kirim";
            return true;
        }
    }
}
