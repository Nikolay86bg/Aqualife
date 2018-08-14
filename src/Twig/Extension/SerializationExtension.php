<?php

/*
 * (c) 411 Marketing
 */

namespace App\Twig\Extension;

/**
 * Class AverageTimeExtension.
 */
class SerializationExtension extends \Twig_Extension
{
    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('unserialize', [$this, 'unserialize']),
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
