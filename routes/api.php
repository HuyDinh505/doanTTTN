<?php

use App\Http\Controllers\BookTicketController;
use App\Http\Controllers\DichVuAnUongController;
use App\Http\Controllers\GheController;
use App\Http\Controllers\LoaiVeController;
use App\Http\Controllers\MoMoPaymentController;
use App\Http\Controllers\PhimController;
use App\Http\Controllers\PhongChieuController;
use App\Http\Controllers\RapPhimController;
use App\Http\Controllers\SuatChieuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TheaterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KhuyenMaiController;
use App\Http\Controllers\VeController;
use App\Http\Controllers\ThucAnController;
use App\Http\Controllers\LoaiPhimController;
use App\Http\Controllers\VaiTroController;
// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');
Route::post('/callback', [MoMoPaymentController::class, 'callback']);

// Public movie routes
Route::get('/phim', [PhimController::class, 'index']); // List all movies
Route::get('/phim-dang-chieu', [PhimController::class, 'getPhimDangChieu']); // List now showing movies
Route::get('/phim-sap-chieu', [PhimController::class, 'getPhimSapChieu']); // List upcoming movies
Route::get('/phim/{id}', [PhimController::class, 'show']); // Show single movie
Route::get('/rap', [RapPhimController::class, 'index']); // List all theaters
// Route::resource('rap', RapPhimController::class);
Route::get('/rap/{ma_rap}', [RapPhimController::class, 'show']); // Get theater details
Route::get('/suatchieu/phim/{ma_phim}', [SuatChieuController::class, 'getByPhim']); // Get showtimes for a movie
Route::get('/phong/dsghe/{ma_phong}', [PhongChieuController::class, 'getGhebyPhong']); // Get seats for a room
Route::get('/phong/{ma_phong}', [PhongChieuController::class, 'show']); // Get room details
Route::resource('loaive', LoaiVeController::class);
Route::resource('dichvuanuong', DichVuAnUongController::class);
// Route::resource('ve', BookTicketController::class);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile routes
    Route::get('/user', [AuthController::class, 'user']);

    // Admin routes
    Route::middleware(\App\Http\Middleware\CheckQuyen::class . ':admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Admin dashboard']);
        });
        // Movie management
        Route::post('/phim', [PhimController::class, 'store']);
        Route::put('/phim/{id}', [PhimController::class, 'update']);
        Route::delete('/phim/{id}', [PhimController::class, 'destroy']);

        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::get('/users/nhan-vien', [UserController::class, 'getNhanVien']);
        Route::get('/users/quan-ly', [UserController::class, 'getQuanLy']);
        Route::get('/vai-tro', [VaiTroController::class, 'index']);
        // Statistics routes
        Route::get('/dashboard/statistics', [DashboardController::class, 'getStatistics']);
        Route::get('/dashboard/revenue', [DashboardController::class, 'getRevenueByDateRange']);
        Route::get('/dashboard/movies', [DashboardController::class, 'getMovieStatistics']);

        // Movie Genre Routes
        Route::get('/loai-phim', [LoaiPhimController::class, 'index']);
        Route::post('/phim/{id}/loai-phim', [LoaiPhimController::class, 'addMovieGenres']);
        Route::delete('/phim/{id}/loai-phim', [LoaiPhimController::class, 'removeMovieGenres']);
        Route::post('/rap', [RapPhimController::class, 'store']);
        Route::post('/rap/{id}', [RapPhimController::class, 'update']);
        Route::delete('/rap/{id}', [RapPhimController::class, 'destroy']);
    });

    // Manager routes
    Route::middleware(\App\Http\Middleware\CheckQuyen::class . ':quan_ly')->group(function () {
        Route::get('/manager/dashboard', function () {
            return response()->json(['message' => 'Manager dashboard']);
        });

        // Quản lý rạp chiếu
        // Route::resource('rap', RapPhimController::class);

        // Quản lý phòng chiếu và thiết bị
        Route::resource('phong', PhongChieuController::class);

        // Quản lý ghế
        Route::resource('ghe', GheController::class);

        // Quản lý suất chiếu
        Route::resource('suatchieu', SuatChieuController::class);

        // Quản lý nhân viên
        Route::get('/nhan-vien', [UserController::class, 'getNhanVien']);
        Route::post('/nhan-vien', [UserController::class, 'createNhanVien']);
        Route::put('/nhan-vien/{id}', [UserController::class, 'updateNhanVien']);
        Route::delete('/nhan-vien/{id}', [UserController::class, 'deleteNhanVien']);

        // Quản lý khuyến mãi
        Route::get('/khuyen-mai', [KhuyenMaiController::class, 'index']);
        Route::post('/khuyen-mai', [KhuyenMaiController::class, 'store']);
        Route::put('/khuyen-mai/{id}', [KhuyenMaiController::class, 'update']);
        Route::delete('/khuyen-mai/{id}', [KhuyenMaiController::class, 'destroy']);

        // Quản lý thống kê
        Route::get('/thong-ke/doanh-thu', [DashboardController::class, 'getRevenueByDateRange']);
        Route::get('/thong-ke/phim', [DashboardController::class, 'getMovieStatistics']);
        Route::get('/thong-ke/tong-quan', [DashboardController::class, 'getStatistics']);

        Route::get('/manager/ve', [VeController::class, 'index']);
        Route::get('/manager/ve/{id}', [VeController::class, 'show']);
        Route::put('/manager/ve/{id}', [VeController::class, 'updateTrangThai']);
    });

    // Staff routes
    Route::middleware(\App\Http\Middleware\CheckQuyen::class . ':nhan_vien')->group(function () {
        Route::get('/staff/dashboard', function () {
            return response()->json(['message' => 'Staff dashboard']);
        });
        // Quản lý vé
        Route::get('/staff/ve', [VeController::class, 'index']);
        Route::get('/staff/ve/{id}', [VeController::class, 'show']);
        Route::put('/staff/ve/{id}', [VeController::class, 'updateTrangThai']);

        // Quản lý dịch vụ ăn uống
        Route::get('/staff/dich-vu-an-uong', [DichVuAnUongController::class, 'index']);
        Route::post('/staff/dich-vu-an-uong', [DichVuAnUongController::class, 'store']);
        Route::put('/staff/dich-vu-an-uong/{id}', [DichVuAnUongController::class, 'update']);
        Route::delete('/staff/dich-vu-an-uong/{id}', [DichVuAnUongController::class, 'destroy']);
        Route::get('/staff/dich-vu-an-uong/{id}', [DichVuAnUongController::class, 'show']);
    });

    // Customer routes
    Route::middleware(\App\Http\Middleware\CheckQuyen::class . ':khach_hang')->group(function () {
        Route::get('/customer/dashboard', function () {
            return response()->json(['message' => 'Customer dashboard']);
        });
        // Booking and payment
        Route::resource('ve', BookTicketController::class);
        Route::post('create-payment', [MoMoPaymentController::class, 'createPayment']);
        Route::post('checkTrasaction', [MoMoPaymentController::class, 'ipn']);
        Route::put('/user/{id}', [UserController::class, 'update']); // Cập nhật tài khoản
        Route::get('/user/{id}/tickets', [VeController::class, 'getTicketsByUser']); // Lấy vé của tôi
        Route::put('/tickets/{ma_ve}/cancel', [VeController::class, 'cancelTicket']); // Hủy vé
    });

    // Movie routes
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::post('/movies', [MovieController::class, 'store']);
    Route::post('/movies/{id}', [MovieController::class, 'update']);
    Route::delete('/movies/{id}', [MovieController::class, 'destroy']);

    Route::post('/update-avatar', [AuthController::class, 'updateAvatar']);

    Route::post('/logout', [AuthController::class, 'logout']);
});

