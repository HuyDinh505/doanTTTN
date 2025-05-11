<?php

namespace App\Http\Controllers;

use App\Models\DatVe;
use App\Models\NguoiDung;
use App\Models\Phim;
use App\Models\SuatChieu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStatistics()
    {
        $totalUsers = NguoiDung::count();
        $totalMovies = Phim::count();
        $totalBookings = DatVe::count();
        $totalShowtimes = SuatChieu::count();

        // Doanh thu theo tháng
        $monthlyRevenue = DatVe::select(
            DB::raw('MONTH(ngay_dat) as month'),
            DB::raw('YEAR(ngay_dat) as year'),
            DB::raw('SUM(tong_gia_tien) as total')
        )
            ->whereYear('ngay_dat', Carbon::now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Top phim có doanh thu cao nhất
        $topMovies = Phim::select('phim.*', DB::raw('SUM(dat_ve.tong_gia_tien) as total_revenue'))
            ->join('suat_chieu', 'phim.ma_phim', '=', 'suat_chieu.ma_phim')
            ->join('dat_ve', 'suat_chieu.ma_suat_chieu', '=', 'dat_ve.ma_suat_chieu')
            ->join('ve_dat', 'dat_ve.ma_ve', '=', 've_dat.ma_ve')
            ->groupBy('phim.ma_phim')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        // Thống kê người dùng mới theo tháng
        $newUsers = NguoiDung::select(
            DB::raw('MONTH(ngay_tao_nd) as month'),
            DB::raw('YEAR(ngay_tao_nd) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->whereYear('ngay_tao_nd', Carbon::now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json([
            'total_users' => $totalUsers,
            'total_movies' => $totalMovies,
            'total_bookings' => $totalBookings,
            'total_showtimes' => $totalShowtimes,
            'monthly_revenue' => $monthlyRevenue,
            'top_movies' => $topMovies,
            'new_users' => $newUsers
        ]);
    }

    public function getRevenueByDateRange(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $revenue = DatVe::select(
            DB::raw('DATE(ngay_dat) as date'),
            DB::raw('SUM(tong_gia_tien) as total')
        )
            ->whereBetween('ngay_dat', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($revenue);
    }

    public function getMovieStatistics()
    {
        $movies = Phim::select(
            'phim.*',
            DB::raw('COUNT(DISTINCT dat_ve.ma_ve) as total_bookings'),
            DB::raw('SUM(dat_ve.tong_gia_tien) as total_revenue')
        )
            ->leftJoin('suat_chieu', 'phim.ma_phim', '=', 'suat_chieu.ma_phim')
            ->leftJoin('dat_ve', 'suat_chieu.ma_suat_chieu', '=', 'dat_ve.ma_suat_chieu')
            ->leftJoin('ve_dat', 'dat_ve.ma_ve', '=', 've_dat.ma_ve')
            ->groupBy('phim.ma_phim')
            ->get();

        return response()->json($movies);
    }
}
