<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\HelpArticle;
use App\Models\HelpCategory;

class HelpController extends Controller
{
    public function index()
    {
        $categories = HelpCategory::with('translations')->get();

        // dd($categories);
        return view('help.index', compact('categories'));
    }

    public function category($slug)
{
    $locale = app()->getLocale(); // текущий язык пользователя

    // Получаем категорию с переводами по текущей локали
    $category = HelpCategory::with(['translations' => function ($query) use ($locale) {
        $query->where('locale', $locale);
    }])->where('slug', $slug)->firstOrFail();

    // Загружаем статьи с переводами по текущей локали
    $articles = $category->articles()
        ->where('published', 1)
        ->with(['translations' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }])
        ->get()
        ->map(function ($article) use ($locale) {
            // Добавляем удобное свойство для вывода перевода
            $article->translated_title = optional($article->translations->first())->title ?? $article->title;
            $article->translated_content = optional($article->translations->first())->content ?? $article->content;
            return $article;
        });

    // Если есть параметр статьи, показываем её
    $selectedArticleSlug = request()->query('article');
    $selectedArticle = null;
    if ($selectedArticleSlug) {
        $selectedArticle = $articles->firstWhere('slug', $selectedArticleSlug);
        // Если есть выбранная статья, используем переводы
        if ($selectedArticle) {
            $selectedArticle->title = $selectedArticle->translated_title;
            $selectedArticle->content = $selectedArticle->translated_content;
        }
    }

    


    return view('help.category', compact('category', 'articles', 'selectedArticle'));
}
}
