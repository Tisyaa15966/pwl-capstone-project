<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ProductModel;
use Dompdf\Dompdf;

class ProdukController extends ResourceController
{
    // Menggunakan nama model standar sesuai bawaan ResourceController
    protected $modelName = 'App\Models\ProductModel';
    protected $format    = 'json';
    
    private $token;

    public function __construct()
    {
        // Mengambil API Key dari file .env
        $this->token = env('MY_API_KEY');
    }

    /**
     * 1. GET /api/products (Index dengan Pagination)
     */
    public function index()
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);

        // $this->model otomatis mengenali ProductModel dari properti $modelName
        $products = $this->model->paginate($perPage, 'default', $page);

        return $this->respond([
            'status'     => true,
            'data'       => $products,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'last_page'    => $this->model->pager->getPageCount(),
                'total_data'   => $this->model->pager->getTotal(),
                'has_next'     => $page < $this->model->pager->getPageCount(),
                'has_prev'     => $page > 1,
            ]
        
            
        ]);
    }

    /**
     * 2. GET /api/products/(:any) (Detail Produk Berdasarkan ID)
     */
    public function show($id = null)
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        if ($id === null || $id === '') {
            return $this->respond([
                'status'  => false,
                'message' => 'ID Produk tidak valid'
            ], 400);
        }

        $product = $this->model->find($id);

        if (!$product) {
            return $this->respond([
                'status'  => false,
                'message' => 'Produk dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        }

        return $this->respond([
            'status' => true,
            'data'   => $product
        ]);
    }

    /**
     * 3. POST /api/products (Tambah Produk baru - Format JSON Input)
     */
    public function create()
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        // Membaca data raw JSON dari Postman Body
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Data JSON tidak boleh kosong'
            ], 400);
        }

        $this->model->insert($data);

        return $this->respondCreated([
            'status'  => true,
            'message' => 'Produk berhasil ditambahkan'
        ]);
    }

    /**
     * 4. PUT atau PATCH /api/products/(:any)
     * Menangani update data via API (Sapu Jagat)
     */
    public function update($id = null)
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        // 1. Cek apakah ID kosong atau tidak valid
        if ($id === null || $id === '') {
            return $this->respond([
                'status'  => false,
                'message' => 'ID Produk tidak valid'
            ], 400);
        }

        // 2. Cari data produk di database menggunakan $this->model bawaan ResourceController
        $product = $this->model->find($id);
        if (!$product) {
            return $this->respond([
                'status'  => false,
                'message' => 'Produk dengan ID ' . $id . ' tidak ditemukan'
            ], 404);
        }

        // 3. Ambil data inputan dari PATCH atau PUT (Metode Sapu Jagat)
        $data = $this->request->getJSON(true); // Cek format JSON Raw

        if (empty($data)) {
            $data = $this->request->getRawInput(); // Cek format x-www-form-urlencoded / PUT Raw
        }

        if (empty($data)) {
            $data = $this->request->getPost(); // Cek format Form-Data biasa
        }

        // 4. Jika ada data yang dikirim, lakukan update ke phpMyAdmin
        if (!empty($data)) {
            $this->model->update($id, $data);

            return $this->respond([
                'status'   => true,
                'message'  => 'Data Jumlah Berhasil Diupdate ke phpMyAdmin!',
                'data_terupdate' => $data
            ]);
        }

        return $this->respond([
            'status'  => false,
            'message' => 'Gagal update, tidak ada perubahan data yang dikirim'
        ], 400);
    }

    public function delete($id = null)
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        $product = $this->model->find($id);
        if (!$product) {
            return $this->respond([
                'status'  => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $this->model->delete($id);

        return $this->respond([
            'status'  => true,
            'message' => 'Data Berhasil Dihapus'
        ]);
    }

    /**
     * 6. GET /api/products/download (Cetak PDF via API)
     */
    public function download()
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        $products = $this->model->findAll();

        $html = view('produk/download_pdf', [
            'products' => $products
        ]);

        $filename = date('Y-m-d-H-i-s') . '-produk.pdf';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($filename, [
            'Attachment' => true
        ]);
        exit(); // Memastikan script berhenti agar tidak ada output buffer lain
    }

    /**
     * Fungsi Autentikasi Token Bearer
     */
    private function authenticate()
    {
        $header = $this->request->getHeaderLine('Authorization');

        if (empty($header)) {
            return false;
        }

        if (!preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return false;
        }

        return $matches[1] === $this->token;
    }

    /**
     * Response Standar JSON jika Token Tidak Valid
     */
    private function unauthorized()
    {
        return $this->respond([
            'status'  => false,
            'message' => 'Unauthorized'
        ], 401);
    }
}