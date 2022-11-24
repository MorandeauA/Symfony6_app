<?php

namespace App\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class Average extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('average', [$this, 'average'])
        ];
    }

    public function average(int $sum, int $nb): float {
        return $sum/$nb;
    }
}
