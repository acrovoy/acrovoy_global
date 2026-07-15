<?php

namespace App\View\Components\Conversation;

use Illuminate\View\Component;

class Drawer extends Component
{
    public function __construct(
        public ?string $subjectType = null,
        public ?int $subjectId = null,
    ) {
    }


    public function render()
    {
        return view('components.conversation.drawer');
    }
}