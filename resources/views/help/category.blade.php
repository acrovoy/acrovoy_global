{{-- resources/views/help/category.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="bg-[#F7F3EA] py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 flex flex-col lg:flex-row gap-6">

       
{{-- LEFT SIDEBAR --}}
<aside
    class="w-full lg:w-[320px]
           bg-[#FFFFFF]
           border border-[#E8E2D6]
           rounded-2xl
           shadow-[0_10px_40px_rgba(0,0,0,0.05)]
           sticky top-6 h-fit overflow-hidden">

    {{-- HEADER --}}
    <div class="px-6 py-5 border-b border-[#F1ECE3] bg-[#FCFBF8] shadow-lg">

    <h2 class="text-[18px] font-semibold tracking-tight text-[#1F1F1F]">
        Help Center
    </h2>

    <p class="text-xs text-[#8A8177] mt-1">
        Browse categories and articles
    </p>

</div>

    {{-- TREE --}}
    <div class="p-3 max-h-[80vh] overflow-y-auto scrollbar-thin scrollbar-thumb-[#E6DDCF] scrollbar-track-transparent">

        @foreach($categories as $treeCategory)

            @include('help.partials.category-tree', [
                'category' => $treeCategory
            ])

        @endforeach

    </div>

</aside>

        {{-- RIGHT: Контент выбранной статьи --}}
        <main class="w-full lg:flex-1
             bg-white
             border border-[#E8E2D6]
             rounded-2xl
             shadow-[0_10px_40px_rgba(0,0,0,0.04)]
             p-10 prose prose-slate max-w-none">
            @if($selectedArticle)
                <h1 class="text-3xl font-bold mb-4">{{ $selectedArticle->title }}</h1>
                <div class="text-gray-800 leading-relaxed">
                    {!! $selectedArticle->content !!}
                </div>
            @else
                <p class="text-gray-600">Выберите статью слева, чтобы увидеть её содержание.</p>
            @endif
        </main>

    </div>
</div>

<script>
function toggleCategory(id, arrowId)
{
    const el = document.getElementById(id);
    const arrow = document.getElementById(arrowId);

    if (!el) return;

    const isOpen = !el.classList.contains('hidden');

    if (isOpen) {
        el.classList.add('hidden');
        localStorage.setItem(id, 'closed');
    } else {
        el.classList.remove('hidden');
        localStorage.setItem(id, 'open');
    }

    if (arrow) {
        arrow.classList.toggle('rotate-90');
    }
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('oembed[url]').forEach(el => {
        const url = el.getAttribute('url');

        let videoId = null;

        if (url.includes('youtube.com/watch')) {
            videoId = new URL(url).searchParams.get('v');
        }

        if (url.includes('youtu.be')) {
            videoId = url.split('/').pop();
        }

        if (videoId) {
            const iframe = document.createElement('iframe');
            iframe.width = "100%";
            iframe.height = "400";
            iframe.src = `https://www.youtube.com/embed/${videoId}`;
            iframe.frameBorder = "0";
            iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
            iframe.allowFullscreen = true;

            el.parentNode.replaceWith(iframe);
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('[id^="cat-"]').forEach(el => {
        const state = localStorage.getItem(el.id);

        if (state === 'open') {
            el.classList.remove('hidden');

            const arrowId = el.id.replace('cat-', 'arrow-');
            const arrow = document.getElementById(arrowId);

            if (arrow) {
                arrow.classList.add('rotate-90');
            }
        }
    });

});
</script>


@endsection
