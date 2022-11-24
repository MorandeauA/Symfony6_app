<?php

namespace App\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Sum extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('sum', [$this, 'sum'])
        ];
    }

    public function sum(array $array): int {

        return array_sum($array);
    }
}
