<?php

namespace App\Http\Controllers\Admin\Help;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\HelpCategory;
use App\Models\HelpArticle;
use App\Models\Language;


class AdminHelpController extends Controller
{
    public function index() {
        // Можно сделать главное окно админки Help
        return view('dashboard.admin.help.index');
    }

    public function categories() {
    // Загружаем все категории с их переводами
    $categories = HelpCategory::with('translations')->get();

    return view('dashboard.admin.help.categories.index', compact('categories'));
}

    public function articles()
{
    $articles = HelpArticle::with('translations')->get();
    return view('dashboard.admin.help.articles.index', compact('articles'));
}

   public function create() {
    $locales = Language::where('is_active', 1)->pluck('code')->toArray();
    return view('dashboard.admin.help.categories.create', compact('locales'));
}

// Сохранение новой категории
public function store(Request $request) {
    $request->validate([
        'slug' => 'required|unique:help_categories,slug',
        'translations.*.name' => 'required|string',
    ]);

    $category = HelpCategory::create([
        'slug' => $request->slug,
    ]);

    // Сохраняем переводы
    foreach ($request->translations as $locale => $data) {
        $category->translations()->create([
            'locale' => $locale,
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? null,
        ]);
    }

    return redirect()->route('admin.help.categories.index')->with('success', 'Category created successfully!');
}

// Форма редактирования категории
public function edit(HelpCategory $category) {
     // Получаем все языки из базы
    $locales = Language::where('is_active', 1)->pluck('code')->toArray();
    // Загружаем все переводы категории
    $category->load('translations');
    return view('dashboard.admin.help.categories.edit', compact('category', 'locales'));
}

// Обновление категории
public function update(Request $request, HelpCategory $category) {
    $request->validate([
        'slug' => 'required|unique:help_categories,slug,' . $category->id,
        'translations.*.name' => 'required|string',
    ]);

    $category->update([
        'slug' => $request->slug,
    ]);

    // Обновляем переводы
    foreach ($request->translations as $locale => $data) {
        $translation = $category->translations()->firstOrNew(['locale' => $locale]);
        $translation->name = $data['name'] ?? '';
        $translation->description = $data['description'] ?? null;
        $translation->save();
    }

    return redirect()->route('admin.help.categories.index')->with('success', 'Category updated successfully!');
}



public function createArticle()
{
    $locales = \App\Models\Language::where('is_active', 1)->pluck('code');
    $categories = HelpCategory::all();
    return view('dashboard.admin.help.articles.create', compact('locales', 'categories'));
}

// Сохранение новой статьи
public function storeArticle(Request $request)
{
    $request->validate([
        'slug' => 'required|unique:help_articles,slug',
        'category' => 'required|string',
        'translations.*.title' => 'required|string',
        'translations.*.content' => 'required|string',
    ]);

    $article = HelpArticle::create([
        'slug' => $request->slug,
        'category' => $request->category,
        'published' => 1,
    ]);

    foreach ($request->translations as $locale => $data) {
        $article->translations()->create([
            'locale' => $locale,
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }

    return redirect()->route('admin.help.articles.index')->with('success', 'Article created successfully.');
}

// Форма редактирования статьи
public function editArticle(HelpArticle $article)
{
    $locales = \App\Models\Language::where('is_active', 1)->pluck('code');
    $categories = HelpCategory::all();
    $article->load('categoryObj');
    return view('dashboard.admin.help.articles.edit', compact('article', 'locales', 'categories'));
}

// Обновление статьи
public function updateArticle(Request $request, HelpArticle $article)
{
    $request->validate([
        'slug' => 'required|unique:help_articles,slug,' . $article->id,
        'category' => 'required|string',
        'translations.*.title' => 'required|string',
        'translations.*.content' => 'required|string',
    ]);

    $article->update([
        'slug' => $request->slug,
        'category' => $request->category,
    ]);

    foreach ($request->translations as $locale => $data) {
        $translation = $article->translations()->firstOrNew(['locale' => $locale]);
        $translation->title = $data['title'];
        $translation->content = $data['content'];
        $translation->save();
    }

    return redirect()->route('admin.help.articles.index')->with('success', 'Article updated successfully.');
}

// Удаление статьи
public function destroyArticle(HelpArticle $article)
{
    $article->delete();
    return redirect()->route('admin.help.articles.index')->with('success', 'Article deleted successfully.');
}


}
