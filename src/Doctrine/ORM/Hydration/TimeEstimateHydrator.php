<?php

/*
 * (c) 411 Marketing
 */

namespace App\Doctrine\ORM\Hydration;

use App\Tools\WorkTime;
use Doctrine\ORM\Internal\Hydration\ArrayHydrator;

/**
 * Class TimeEstimateHydrator.
 */
class TimeEstimateHydrator extends ArrayHydrator
{
    /**
     * @return WorkTime[]
     */
    protected function hydrateAllData()
    {
        $result = [];

        while ($data = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) {
            $key = preg_grep('/time_estimate/', array_keys($data));

            $result[] = new WorkTime(new \DateTime($data[array_values($key)[0]]));
        }

        return $result;
    }
}
