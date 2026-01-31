<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminBannersController extends Controller
{
    // Тестовые баннеры
    private $banners = [
        [
            'id' => 1,
            'title' => 'Homepage Banner',
            'image' => '/images/banners/banner1.jpg',
            'created_by' => 'Admin',
            'status' => 'active',
            'created_at' => '2026-01-01',
        ],
        [
            'id' => 2,
            'title' => 'Sidebar Banner',
            'image' => '/images/banners/banner2.jpg',
            'created_by' => 'Admin',
            'status' => 'inactive',
            'created_at' => '2026-01-02',
        ],
    ];

    public function index(Request $request)
    {
        // Здесь можно добавить фильтры по статусу
        $statusFilter = $request->get('status', '');
        $banners = $this->banners;

        if ($statusFilter) {
            $banners = array_filter($banners, fn($b) => $b['status'] === $statusFilter);
        }

        return view('dashboard.admin.banners.index', [
            'banners' => $banners,
            'status' => $statusFilter,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.banners.create');
    }

    public function store(Request $request)
    {
        // Здесь логика загрузки изображения и сохранения баннера
        // $request->file('image')->store('banners', 'public');
        return redirect()->route('admin.banners.index')->with('success', 'Banner uploaded!');
    }
}

