<?php

namespace App\Support;

use Illuminate\Support\Str;

class HashtagFormatter
{
    public static function extract(?string $text): array
    {
        preg_match_all('/(?<![\p{L}\p{N}_])#([\p{L}\p{N}_]{1,100})/u', (string) $text, $matches);

        return collect($matches[1] ?? [])->map(fn ($tag) => mb_strtolower($tag))
            ->unique()->take(20)->values()->all();
    }

    public static function slug(string $name): string
    {
        $slug = Str::slug($name, '-', 'ar');
        return $slug !== '' ? $slug : rawurlencode($name);
    }

    public static function linkify(?string $text): string
    {
        $safe = e((string) $text);
        return preg_replace_callback('/(?<![\p{L}\p{N}_])#([\p{L}\p{N}_]{1,100})/u', function ($match) {
            return '<a class="post-hashtag" href="'.url('/hashtag/'.self::slug($match[1])).'">#'.$match[1].'</a>';
        }, $safe) ?? $safe;
    }
}
