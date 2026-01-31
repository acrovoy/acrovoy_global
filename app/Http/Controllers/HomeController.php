<?php

namespace App\Http\Controllers;
use App\Models\Category;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {

        $catalogCategories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
        
        // Здесь можно подготовить данные для карточек, поставщиков и коллекций
        return view('main', [

            'catalogCategories' => $catalogCategories,
            
            'categories' => [
                ['title' => 'Outdoor Furniture', 'image' => 'images/categories/outdoor.jpg', 'link' => '#'],
                ['title' => 'Rattan & Woven Furniture', 'image' => 'images/categories/rattan.jpg', 'link' => '#'],
                ['title' => 'Restaurant Seating', 'image' => 'images/categories/restaurant.jpg', 'link' => '#'],
                ['title' => 'Hotel Room Furniture', 'image' => 'images/categories/hotel.jpg', 'link' => '#'],
                ['title' => 'Lighting & Decor', 'image' => 'images/categories/lighting.jpg', 'link' => '#'],
                ['title' => 'Lobby & Lounge Collections', 'image' => 'images/categories/lobby.jpg', 'link' => '#'],
                ['title' => 'Natural Accessories', 'image' => 'images/categories/accessories.jpg', 'link' => '#'],
                ['title' => 'Pool & Beach Areas', 'image' => 'images/categories/pool.jpg', 'link' => '#'],
            ],
            'advantages' => [
                ['title' => 'Global Supplier Network', 'text' => 'Manufacturers from Asia, Europe, Middle East & Latin America.'],
                ['title' => 'Verified & Trusted', 'text' => 'Every supplier is screened for quality and reliability.'],
                ['title' => 'Custom Projects', 'text' => 'Bulk orders, custom design, CAD files, OEM production.'],
                ['title' => 'Fast Quotes', 'text' => 'Connect directly with manufacturers — no middlemen.'],
            ],
            'featuredSuppliers' => [
    [
        'name' => 'ACME Furniture',
        'country' => 'Vietnam',
        'products' => 'Outdoor Furniture',
        'image' => 'images/suppliers/acme.jpg',
        'link' => '#'
    ],
    [
        'name' => 'Rattan Co.',
        'country' => 'Indonesia',
        'products' => 'Rattan & Woven',
        'image' => 'images/suppliers/rattan.jpg',
        'link' => '#'
    ],
    [
        'name' => 'Markell Rattan',
        'country' => 'Saudi Arabia',
        'products' => 'Outdoor Furniture',
        'image' => 'images/suppliers/markell.jpg',
        'link' => '#'
    ],
            ],
            'collections' => [
    [
        'title' => 'Bali Outdoor Collection',
        'image' => 'images/collections/bali.jpg',
        'link'  => '#'
    ],
    [
        'title' => 'Natural Rattan Lounge Sets',
        'image' => 'images/collections/rattan.jpg',
        'link'  => '#'
    ],
    [
        'title' => 'Luxury Restaurant Seating',
        'image' => 'images/collections/restaurant.jpg',
        'link'  => '#'
    ],
    [
        'title' => 'Poolside Daybeds',
        'image' => 'images/collections/poolside.jpg',
        'link'  => '#'
    ],
    [
        'title' => 'Hotel Lobby Accent Pieces',
        'image' => 'images/collections/lobby.jpg',
        'link'  => '#'
    ],
    [
        'title' => 'Eco-Friendly Décor',
        'image' => 'images/collections/eco.jpg',
        'link'  => '#'
    ],
],
            'countries' => [
                'Indonesia','Vietnam','China','Turkey','Poland','Spain','Ukraine'
            ]
        ]);
    }
}
