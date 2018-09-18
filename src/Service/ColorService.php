<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 8/25/2018
 * Time: 5:08 PM
 */

namespace App\Service;

class ColorService
{
    const COLORS = [
        'bg-light-blue-active',
        'bg-aqua-active',
        'bg-green-active',
        'bg-yellow-active',
        'bg-red-active',
//        'bg-gray-active',
        'bg-navy-active',
        'bg-teal-active',
        'bg-purple-active',
        'bg-orange-active',
        'bg-maroon-active',
//        'bg-black-active',
    ];

    const COLOR_NAMES = [
        'light-blue',
        'aqua',
        'green',
        'yellow',
        'red',
        'navy',
        'teal',
        'purple',
        'orange',
        'maroon',
    ];

    /**
     * @param $id
     * @return array
     */
    public function getColorFromId($id)
    {
        return self::COLORS[substr($id, -1)];
    }

    public function getColorNameFromId($id)
    {
        return self::COLOR_NAMES[substr($id, -1)];
    }
}