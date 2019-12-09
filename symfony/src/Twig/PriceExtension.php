<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extensions\IntlExtension as TwigIntlExtension;
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
        $formatter = twig_get_number_formatter($locale, 'currency');

        return $formatter->formatCurrency($number / 100, $currency);
    }
}