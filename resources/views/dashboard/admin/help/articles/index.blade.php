@extends('dashboard.admin.help.layout')

@section('settings-content')
<h1 class="text-2xl font-bold mb-4">Help Articles</h1>

<div class="mb-4">
    <a href="{{ route('admin.help.articles.create') }}" 
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        Add New Article
    </a>
</div>

<table class="w-full border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-100">
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Slug</th>
            <th class="border px-4 py-2">Category</th>
            <th class="border px-4 py-2">Title (all languages)</th>
            <th class="border px-4 py-2">Content (first 100 chars)</th>
            <th class="border px-4 py-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($articles as $article)
        <tr>
            <td class="border px-4 py-2">{{ $article->id }}</td>
            <td class="border px-4 py-2">{{ $article->slug }}</td>
            <td class="border px-4 py-2">{{ $article->category }}</td>
            
            {{-- Title по языкам --}}
            <td class="border px-4 py-2">
                @foreach($article->translations as $translation)
                    <div class="mb-1">
                        <strong>{{ strtoupper($translation->locale) }}:</strong> {{ $translation->title }}
                    </div>
                @endforeach
            </td>

            {{-- Content по языкам (частично) --}}
            <td class="border px-4 py-2">
                @foreach($article->translations as $translation)
                    <div class="mb-1">
                        <strong>{{ strtoupper($translation->locale) }}:</strong>
                        {{ \Illuminate\Support\Str::limit(strip_tags($translation->content), 100) }}
                    </div>
                @endforeach
            </td>

            {{-- Actions --}}
            <td class="border px-4 py-2 space-x-2">
                <a href="{{ route('admin.help.articles.edit', $article->id) }}" 
                   class="px-2 py-1 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition">Edit</a>
                
                <form action="{{ route('admin.help.articles.destroy', $article->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this article?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
