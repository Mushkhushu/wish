<?php

namespace App\Censurator;

class Censurator
{

    public function __construct()
    {

    }

    public function purify(string $text): string
    {
        $file = '../data/forbidden_words.txt';
        $forbiddenWords = file($file);

        foreach ($forbiddenWords as $word) {
            $word = str_ireplace(PHP_EOL, '', $word);
            $replacement = str_repeat('*', strlen($word));
            $text = str_ireplace($word, $replacement, $text);
        }
        return $text;
    }
}