<?php

namespace App\Censurator;

class Censurator
{
    private $forbiddenWords = ['lapin', 'canard', 'éléphant'];

    public function __construct()
    {

    }

    public function purify(string $text): string
    {
        foreach ($this->forbiddenWords as $word) {
            $text = str_ireplace($word, '*', $text);
        }
        return $text;
    }
}