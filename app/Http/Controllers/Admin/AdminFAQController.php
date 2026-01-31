<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminFAQController extends Controller
{
    // Тестовые данные вместо базы
    private $faqs = [
        ['id' => 1, 'question' => 'How does messaging work?', 'answer' => 'Buyers and sellers can communicate directly through the Message Center.'],
        ['id' => 2, 'question' => 'How do I become a seller?', 'answer' => 'Register an account and switch to Seller mode in your dashboard.'],
        ['id' => 3, 'question' => 'Is it free to use?', 'answer' => 'Yes, the platform is free to start. Premium plans are optional.'],
    ];

    public function index()
    {
        $faqs = $this->faqs;
        return view('dashboard.admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('dashboard.admin.faq.create');
    }

    public function store(Request $request)
    {
        // В реальном проекте добавь в БД
        // Для теста — редирект с флеш-сообщением
        return redirect()->route('admin.faq.index')->with('success', 'FAQ added successfully!');
    }

    public function edit($id)
    {
        $faq = collect($this->faqs)->firstWhere('id', $id);
        return view('dashboard.admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, $id)
    {
        // Обновление в БД
        return redirect()->route('admin.faq.index')->with('success', 'FAQ updated successfully!');
    }

    public function destroy($id)
    {
        // Удаление из БД
        return redirect()->route('admin.faq.index')->with('success', 'FAQ deleted successfully!');
    }
}
