<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\LunasController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UnitController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/menus', [MenuController::class, 'index'])->name('indexmenu');
    Route::get('/datamenu', [MenuController::class, 'datamenu'])->name('datamenu');
    Route::post('/editmenu', [MenuController::class, 'edit'])->name('editmenu');
    Route::post('/storemenu', [MenuController::class, 'store'])->name('storemenu');
    Route::post('/updatemenu', [MenuController::class, 'update'])->name('updatemenu');
    Route::post('/hapusmenu', [MenuController::class, 'destroy'])->name('hapusmenu');

    Route::get('/users', [UserController::class, 'index'])->name('indexuser');
    Route::get('/datauser', [UserController::class, 'datauser'])->name('datauser');
    Route::post('/edituser', [UserController::class, 'edit'])->name('edituser');
    Route::post('/storeuser', [UserController::class, 'store'])->name('storeuser');
    Route::post('/updateuser', [UserController::class, 'update'])->name('updateuser');
    Route::post('/hapususer', [UserController::class, 'destroy'])->name('hapususer');

    Route::get('/roles', [RoleController::class, 'index'])->name('indexrole');
    Route::get('/datarole', [RoleController::class, 'datarole'])->name('datarole');
    Route::get('/showrole', [RoleController::class, 'show'])->name('showrole');
    Route::post('/editrole', [RoleController::class, 'edit'])->name('editrole');
    Route::post('/storerole', [RoleController::class, 'store'])->name('storerole');
    Route::post('/updaterole', [RoleController::class, 'update'])->name('updaterole');
    Route::post('/hapusrole', [RoleController::class, 'destroy'])->name('hapusrole');

    Route::get('/produk', [ProdukController::class, 'index'])->name('masterdata');
    Route::get('/getqtyunit', [ProdukController::class, 'getqtyunit'])->name('getqtyunit');
    Route::get('/dataproduk', [ProdukController::class, 'listproduk'])->name('dataproduk');
    Route::post('/simpanproduk', [ProdukController::class, 'store'])->name('simpanproduk');
    Route::post('/updateproduk', [ProdukController::class, 'update'])->name('updateproduk');
    Route::post('/editproduk', [ProdukController::class, 'edit'])->name('editproduk');
    Route::post('/hapusproduk', [ProdukController::class, 'destroy'])->name('hapusproduk');

    Route::get('/uom', [UnitController::class, 'index'])->name('masteruom');
    Route::get('/datauom', [UnitController::class, 'listunit'])->name('datauom');
    Route::post('/simpanuom', [UnitController::class, 'store'])->name('simpanuom');
    Route::post('/updateuom', [UnitController::class, 'update'])->name('updateuom');
    Route::post('/edituom', [UnitController::class, 'edit'])->name('edituom');
    Route::post('/hapusuom', [UnitController::class, 'destroy'])->name('hapusuom');

    Route::get('/kasir', [KasirController::class, 'index'])->name('datakasir');
    Route::post('/listproduk', [KasirController::class, 'listproduk'])->name('getproduk');
    Route::get('/getharga', [KasirController::class, 'getharga'])->name('dataharga');
    Route::get('/getuom', [KasirController::class, 'getuom'])->name('getuom');
    Route::get('/listtempkasir', [KasirController::class, 'listtempkasir'])->name('datatempkasir');
    Route::post('/simpantempkasir', [KasirController::class, 'storetemp'])->name('tempkasir');
    Route::post('/hapustempkasir', [KasirController::class, 'destroy'])->name('hapustempkasir');
    Route::get('/getamount', [KasirController::class, 'getamount'])->name('getamount');
    Route::post('/addtransaksi', [KasirController::class, 'store'])->name('addtransaksi');
    Route::get('/cetak', [KasirController::class, 'cetak'])->name('cetakdata');
    Route::post('/reset', [KasirController::class, 'resetdata'])->name('resetdata');

    Route::get('/penjualan2', [ReportController::class, 'index2'])->name('penjualan2');
    Route::get('/detailpenjualan2', [ReportController::class, 'detailjual2'])->name('detailpenjualan2');

    Route::get('/pelunasan', [LunasController::class, 'index'])->name('translunas');
    Route::post('/listinv', [LunasController::class, 'listinv'])->name('getinv');
    Route::post('/listprodukdp', [LunasController::class, 'listprodukdp'])->name('listprodukdp');
    Route::post('/addlunas', [LunasController::class, 'store'])->name('addlunas');
    Route::get('/cetaklunas', [LunasController::class, 'cetak'])->name('cetakdatalunas');

    Route::get('/rptpenjualan', [ReportController::class, 'index'])->name('penjualan');
    Route::get('/datapenjualan', [ReportController::class, 'listjual'])->name('datapenjualan');
    Route::get('/rptdetailpenjualan', [ReportController::class, 'detail'])->name('dtlpenjualan');
    Route::get('/detailpenjualan', [ReportController::class, 'detailjual'])->name('detailpenjualan');
    Route::get('/detailjual', [ReportController::class, 'viewdetailjual'])->name('detailjual');
    Route::get('/cetakrpt/{id}', [ReportController::class, 'cetak'])->name('cetakrpt');
    Route::get('/datakeu', [ReportController::class, 'viewdatakeu'])->name('datakeu');
    Route::get('/listdatakeu', [ReportController::class, 'listdatakeu'])->name('listdatakeu');
    Route::get('/kredit', [ReportController::class, 'viewkredit'])->name('kredit');
    Route::get('/detailkredit', [ReportController::class, 'detailkredit'])->name('detailkredit');
    Route::post('/hapusinv', [ReportController::class, 'destroyinv'])->name('hapusinv');
    Route::post('/getlaba', [ReportController::class, 'getamount'])->name('getlaba');
    Route::get('/rptproduk', [ReportController::class, 'viewproduk'])->name('rptproduk');
    Route::get('/daftarharga', [ReportController::class, 'daftarharga'])->name('daftarharga');
});
