<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductModel;

class ProdukController extends BaseController
{
    protected $productModel;

    public function __construct()
    {
        helper(['form', 'url']);

        $this->productModel = new ProductModel();
    }

    public function index()
    {
        return view('produk/index', [
            'products' => $this->productModel->findAll()
        ]);
    }

    public function create()
    {
        $dataFoto = $this->request->getFile('foto');

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah') 
        ];

        if ($dataFoto->isValid()) {
            $fileName = $dataFoto->getRandomName(); 
            $dataFoto->move('img/', $fileName);
            
            $dataForm['foto'] = $fileName;
        }

        $this->productModel->insert($dataForm);

        // Diubah sedikit ke standar redirect CI4 agar tidak error method
        return redirect()->to('produk')->with('success', 'Data Berhasil Ditambah');
    } 

        public function edit($id)
    {
        $dataProduk = $this->productModel->find($id);

        $dataForm = [
            'nama' => $this->request->getPost('nama'),
            'harga' => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah') 
        ];

        if ($this->request->getPost('check') == 1) {
            if ($dataProduk['foto'] != '' and file_exists("img/" . $dataProduk['foto'] . "")) {
                unlink("img/" . $dataProduk['foto']);
            }

            $dataFoto = $this->request->getFile('foto');

            if ($dataFoto->isValid()) {
                $fileName = $dataFoto->getRandomName();
                $dataFoto->move('img/', $fileName);
                
                $dataForm['foto'] = $fileName;
            }
        }

        $this->productModel->update($id, $dataForm);

        return redirect('produk')->with('success', 'Data Berhasil Diubah');
    }

    public function delete($id)
    {
        $dataProduk = $this->productModel->find($id);
        $this->productModel->delete($id);

        return redirect('produk')->with('success', 'Data Berhasil Dihapus');
    }
    public function update($id)
    {
        $dataFoto = $this->request->getFile('foto');

        // 1. Ambil data inputan teks dari form modal edit
        $dataForm = [
            'nama'   => $this->request->getPost('nama'),
            'harga'  => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah') 
        ];

        // 2. Ambil data produk lama untuk tahu nama file foto yang sekarang
        $produkLama = $this->productModel->find($id);

        // 3. Cek apakah user mencentang checkbox ganti foto dan file-nya valid
        if ($this->request->getPost('check') == '1' && $dataFoto->isValid()) {
            
            // Hapus file foto lama di folder img/ jika filenya ada
            if ($produkLama['foto'] && file_exists('img/' . $produkLama['foto'])) {
                unlink('img/' . $produkLama['foto']);
            }

            // Upload foto yang baru
            $fileName = $dataFoto->getRandomName(); 
            $dataFoto->move('img/', $fileName);
            
            $dataForm['foto'] = $fileName;
        } else {
            // Jika tidak centang ganti foto, tetap pakai nama foto yang lama
            $dataForm['foto'] = $produkLama['foto'];
        }

        // 4. Jalankan fungsi update CodeIgniter 4 berdasarkan ID produk
        $this->productModel->update($id, $dataForm);

        return redirect()->to('produk')->with('success', 'Data Berhasil Diubah');
    }
}