<?php

namespace App\Classe;

use App\Entity\Category;

class Search
{
    /**
     * @var string|null
     */
    public string|null $string = '';

    /**
     * @var Category[]
     */
    public array $categories = [];
}