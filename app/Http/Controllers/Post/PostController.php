<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PostController extends Controller
{
    public function show(): View
    {
        $post = [
            'id' => 1,
            'slug' => 'creating-timeless-hospitality-spaces',
            'title' => 'Creating Timeless Spaces for Modern Hospitality',
            'excerpt' => 'Discover how carefully selected furniture, materials and thoughtful design can transform hospitality interiors into memorable experiences.',
            'content' => [
                'Hospitality design is about more than creating beautiful interiors. It is about shaping experiences, building atmosphere and making every guest feel welcome.',
                'From boutique hotels to contemporary restaurants, furniture plays an essential role in defining the character of a space. The right combination of materials, textures and proportions can turn an ordinary interior into something truly memorable.',
                'For this project, we focused on natural materials, timeless silhouettes and carefully selected furniture pieces that balance visual appeal with everyday functionality.',
                'Our approach brings together comfort, durability and refined aesthetics — creating spaces that remain relevant beyond seasonal trends.',
            ],
            'cover' => asset('images/home/banners/hotel.jpg'),
            'images' => [
                asset('images/home/banners/hero.jpg'),
                asset('images/home/banners/outdoor.jpg'),
            ],
            'category' => 'Design & Inspiration',
            'published_at' => now()->subHours(2),
            'views' => 348,
            'likes' => 24,
            'comments_count' => 8,
            'author' => [
                'name' => 'Studio Design',
                'type' => 'Interior Design Studio',
                'location' => 'Milan, Italy',
                'avatar' => asset('images/home/avatar-placeholder.jpg'),
                'website' => '#',
                'verified' => true,
                'followers' => 1240,
            ],
            'products' => [
                [
                    'id' => 101,
                    'name' => 'Modern Lounge Chair',
                    'category' => 'Hotel Furniture',
                    'price' => 'From $420',
                    'image' => asset('images/home/banners/hotel.jpg'),
                    'supplier' => 'Premium Furniture Co.',
                    'url' => '#',
                ],
                [
                    'id' => 102,
                    'name' => 'Outdoor Dining Collection',
                    'category' => 'Outdoor Furniture',
                    'price' => 'From $890',
                    'image' => asset('images/home/banners/outdoor.jpg'),
                    'supplier' => 'Outdoor Living Studio',
                    'url' => '#',
                ],
                [
                    'id' => 103,
                    'name' => 'Contemporary Hotel Seating',
                    'category' => 'Hospitality Seating',
                    'price' => 'From $350',
                    'image' => asset('images/home/banners/hero.jpg'),
                    'supplier' => 'Modern Contract Furniture',
                    'url' => '#',
                ],
            ],
        ];

       $comments = [
    [
        'author' => 'Alex Morgan',
        'avatar' => asset('images/333.JPG'),
        'date' => now()->subMinutes(35),
        'text' => 'Beautiful combination of natural materials and contemporary furniture. Really inspiring project!',
        'likes' => 4,
    ],
    [
        'author' => 'Emma Wilson',
        'avatar' => asset('images/444.JPG'),
        'date' => now()->subMinutes(58),
        'text' => 'I especially like the balance between functionality and aesthetics. Great work!',
        'likes' => 2,
    ],
    [
        'author' => 'Daniel Chen',
        'avatar' => asset('images/555.png'),
        'date' => now()->subHours(1),
        'text' => 'Could you share more details about the outdoor furniture collection?',
        'likes' => 1,
    ],
];

        return view('post.post', compact('post', 'comments'));
    }
}