@extends('dashboard.admin.help.layout')

@section('settings-content')
<div class="flex flex-col gap-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Help Articles</h1>

        {{-- Кнопка добавления новой статьи --}}
        <a href="{{ route('admin.help.articles.create') }}"
           class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
            + Add New Article
        </a>
    </div>

    {{-- Таблица статей --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 font-medium text-gray-600">ID</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Slug</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Category</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Title (all languages)</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Content (first 100 chars)</th>
                    <th class="px-5 py-3 font-medium text-gray-600 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($articles as $article)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $article->id }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $article->slug }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $article->category }}</td>

                        {{-- Title по языкам --}}
                        <td class="px-5 py-3">
                            @foreach($article->translations as $translation)
                                <div class="mb-1">
                                    <strong class="text-gray-500">{{ strtoupper($translation->locale) }}:</strong>
                                    <span class="text-gray-900">{{ $translation->title }}</span>
                                </div>
                            @endforeach
                        </td>

                        {{-- Content по языкам (частично) --}}
                        <td class="px-5 py-3">
                            @foreach($article->translations as $translation)
                                <div class="mb-1">
                                    <strong class="text-gray-500">{{ strtoupper($translation->locale) }}:</strong>
                                    <span class="text-gray-700">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($translation->content), 100) }}
                                    </span>
                                </div>
                            @endforeach
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3 text-right space-x-2">
                            {{-- Edit --}}
                            <a href="{{ route('admin.help.articles.edit', $article->id) }}"
                               class="text-sm text-gray-700 hover:underline mr-3">
                                Edit
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('admin.help.articles.destroy', $article->id) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Delete this article?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

                @if($articles->isEmpty())
                    <tr>
                        <td colspan="6" class="px-5 py-6 text-center text-gray-500">
                            No articles found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Пагинация --}}
        @if(method_exists($articles, 'links'))
        <div class="mt-4 px-5 py-3">
            {{ $articles->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection
