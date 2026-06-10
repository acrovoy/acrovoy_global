@extends('dashboard.admin.help.layout')

@section('settings-content')
<div class="flex flex-col gap-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Help Articles</h1>

        {{-- Кнопка добавления новой статьи --}}
        <a href="{{ route('admin.help.articles.create') }}"
           class="inline-flex items-center gap-2 mt-3 px-4 py-2
           text-sm font-medium text-gray-700
           bg-white border border-gray-200
           rounded-lg
           hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900
           active:scale-[0.98]
           transition-all duration-150 shadow-sm">
            + Add New Article
        </a>
    </div>

    {{-- Таблица статей --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-5 py-3 font-medium text-gray-600">ID</th>
                    <th class="px-5 py-3 font-medium text-gray-600">Title</th>
                    
                    <th class="px-5 py-3 font-medium text-gray-600">Category</th>
                    
                    
                    <th class="px-5 py-3 font-medium text-gray-600 text-right">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach($articles as $article)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $article->id }}</td>

                        {{-- Title по языкам --}}
                        <td class="px-5 py-3">
                            
                            <span class="text-gray-900">
         {{ optional($article->translations->firstWhere('locale', 'en'))->title ?? '—' }}
    </span>
                        </td>


                        
                        <td class="px-5 py-3 text-gray-700">
                         @php
        $category = \App\Models\HelpCategory::with('parent.translations')
            ->where('slug', $article->category)
            ->first();
    @endphp
    <div>
        <div class="font-medium">
            {{ $category?->translations->firstWhere('locale', 'en')->name ?? '—' }}
        </div>

        @if($category?->parent)
            <div class="text-xs text-yellow-500 mt-1">
                 {{ optional($category->parent->translations->firstWhere('locale', 'en'))->name }}
            </div>
        @endif
    </div>

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
