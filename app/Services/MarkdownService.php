<?php

namespace App\Services;

use League\CommonMark\CommonMarkConverter;

class MarkdownService
{
    protected CommonMarkConverter $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function toHtml(string $text): string
    {
        return $this->converter
            ->convert($text)
            ->getContent();
    }
}