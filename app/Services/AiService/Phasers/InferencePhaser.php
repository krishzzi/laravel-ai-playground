<?php

namespace App\Services\AiService\Phasers;

use Illuminate\Support\Str;

class InferencePhaser
{
    protected string $content;
    protected ?string $cleanContent = null;

    public function __construct(string $content)
    {
        $this->content = trim($content);
        $this->cleanContent = null;
    }

    public static function make(string $content): static
    {
        return new static($content);
    }

    public function phrase(): string
    {
        if ($this->cleanContent !== null) {
            return $this->cleanContent;
        }

        return $this->cleanContent = $this->formatter();
    }

    protected function formatter(): string
    {
        if ($this->content === '') {
            return '';
        }

        return $this->parseMixedContent($this->content);
    }

    protected function parseMixedContent(string $content): string
    {
        $parts = preg_split('/(```[\s\S]*?```)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($parts === false) {
            return $this->formatTextBlock($content);
        }

        $html = [];

        foreach ($parts as $part) {
            if ($this->isCodeFence($part)) {
                $html[] = $this->formatCodeBlock($part);
            } else {
                $formatted = $this->formatTextBlock($part);

                if ($formatted !== '') {
                    $html[] = $formatted;
                }
            }
        }

        return implode("\n", array_filter($html));
    }

    protected function isCodeFence(string $text): bool
    {
        return preg_match('/^```[\w#+.-]*\R[\s\S]*\R?```$/', trim($text)) === 1
            || preg_match('/^```[\w#+.-]*[\s\S]*```$/', trim($text)) === 1;
    }

    protected function formatCodeBlock(string $block): string
    {
        $block = trim($block);

        preg_match('/^```([\w#+.-]*)\R?([\s\S]*?)\R?```$/', $block, $matches);

        $language = trim($matches[1] ?? '');
        $code = trim($matches[2] ?? '');

        $languageClass = $language !== ''
            ? ' class="language-' . e($language) . '"'
            : '';

        return '<pre class="ai-code-block"><code' . $languageClass . '>' . e($code) . '</code></pre>';
    }

    protected function formatTextBlock(string $text): string
    {
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        $paragraphs = preg_split('/\R{2,}/', $text);

        if ($paragraphs === false) {
            $paragraphs = [$text];
        }

        $html = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $paragraph = e($paragraph);
            $paragraph = $this->formatInlineCode($paragraph);
            $paragraph = nl2br($paragraph);

            $html[] = '<p>' . $paragraph . '</p>';
        }

        return implode("\n", $html);
    }

    protected function formatInlineCode(string $text): string
    {
        return preg_replace_callback('/`([^`\n]+)`/', function ($matches) {
            return '<code>' . $matches[1] . '</code>';
        }, $text) ?? $text;
    }
}
