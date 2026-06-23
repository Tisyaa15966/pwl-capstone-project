<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductModel;
use Dompdf\Dompdf;
use CodeIgniter\API\ResponseTrait; 

class ProdukController extends BaseController
{
    use ResponseTrait; 
    
    // PERBAIKAN: Memuat helper form dan url secara global untuk memperbaiki error form_open_multipart()
    protected $helpers = ['form', 'url'];
    
    protected $productModel;
    private $token;

    public function __construct()
    { 
        $this->productModel = new ProductModel(); 
        $this->token = env('MY_API_KEY');
    }

    /**
     * 1. GET /api/products (API) ATAU /produk (Web Sidebar)
     */
    public function index()
    {
        // Cek token HANYA jika URL-nya mengandung kata 'api'
        if (str_contains($this->request->getUri()->getPath(), 'api')) {
            if (!$this->authenticate()) {
                return $this->unauthorized();
            }
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);

        // Perbaikan: Gunakan $this->productModel (bukan $this->model)
        $products = $this->productModel->paginate($perPage, 'default', $page);

        // Jika diakses dari API (Postman)
        if (str_contains($this->request->getUri()->getPath(), 'api')) {
            return $this->respond([
                'data' => $products,
                'pagination' => [
                    'current_page' => $page,
                    'per_page'     => $perPage,
                    'last_page'    => $this->productModel->pager->getPageCount(),
                    'total_data'   => $this->productModel->pager->getTotal(),
                    'has_next'     => $page < $this->productModel->pager->getPageCount(),
                    'has_prev'     => $page > 1,
                ]
            ]);
        }

        // Jika diakses dari Web Sidebar localhost biasa
        return view('produk/index', [
            'products' => $products,
            'pager'    => $this->productModel->pager
        ]);
    }

    /**
     * 2. GET /api/products/(:any) (Detail per ID)
     */
    public function show($id = null)
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        $product = $this->productModel->find($id);

        if (!$product) {
            return $this->failNotFound('Produk tidak ditemukan');
        }

        return $this->respond($product);
    } 

    /**
     * 3. POST /api/products (Tambah Data JSON)
     */
    public function create()
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->respond(['status' => false, 'message' => 'Data JSON kosong'], 400);
        }

        $this->productModel->insert($data);

        return $this->respondCreated([
            'status'  => true,
            'message' => 'Produk berhasil ditambahkan'
        ]);
    } 

    /**
     * 4. POST /api/products/update/(:any) (Update Data via API)
     */
    public function update($id = null)
{
    if (!$this->authenticate()) {
        return $this->unauthorized();
    }

    if (!$this->model->find($id)) {
        return $this->failNotFound('Produk tidak ditemukan');
    }

    $data = $this->request->getJSON(true);

    $this->model->update($id, $data);

    return $this->respond([
        'message' => 'Produk berhasil diperbarui'
    ]);
}

    /**
     * 5. DELETE /api/products/(:any) (Hapus Data via API)
     */
    public function delete($id = null)
    {
        if (!$this->authenticate()) {
            return $this->unauthorized();
        }

        // Perbaikan: Gunakan $this->productModel
        if (!$this->productModel->find($id)) {
            return $this->failNotFound('Produk tidak ditemukan');
        }

        $this->productModel->delete($id);

        return $this->respond([
            'status'  => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    /**
     * 6. Cetak PDF
     */
    public function download()
    {
        $products = $this->productModel->findAll();

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
    }

    /**
     * Sistem Keamanan Token Bearer
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

    private function unauthorized()
    {
        return $this->respond([
            'status'  => false,
            'message' => 'Unauthorized'
        ], 401);
    }
}