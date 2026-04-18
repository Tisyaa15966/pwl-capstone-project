<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('login', 'AuthController::login'); // Menampilkan form
$routes->post('login', 'AuthController::login'); // Memproses data
$routes->get('logout', 'AuthController::logout'); // Keluar

$routes->get('/produk', 'ProdukController::index');
$routes->get('/keranjang', 'TransaksiController::index');