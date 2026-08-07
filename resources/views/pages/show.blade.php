@if($page->template === 'legal')
    @include('pages.templates.legal')
@elseif($page->template === 'corporate')
    @include('pages.templates.corporate')
@elseif($page->template === 'landing')
    
@endif