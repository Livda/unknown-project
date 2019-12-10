<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($number, $currency = null, $locale = null)
    {
        $locale = null !== $locale ? $locale : \Locale::getDefault();

        $formatter = \NumberFormatter::create($locale, \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($number / 100, $currency);
    }
}
