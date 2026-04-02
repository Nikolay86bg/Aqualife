<?php

/*
 * (c) 411 Marketing
 */

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class SerializationExtension
 * @package App\Twig\Extension
 */
class SerializationExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('unserialize', [$this, 'unserialize']),
        ];
    }

    /**
     * @param null $value
     *
     * @return string
     */
    public function unserialize($value = null)
    {
        if (null === $value) {
            return '';
        }

        return unserialize($value);
    }
}
