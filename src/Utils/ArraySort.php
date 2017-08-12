<?php
/**
 * This file is part of the IBanking library.
 *
 * (c) Edi Septriyanto <me@masedi.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace IBanking\Utils;

/**
 * Allows sorting multi-dimensional arrays by a specific key and in asc or desc order.
 *
 * Ref: https://stackoverflow.com/a/7443948
 */
class ArraySort
{
    public static function comparer()
    {
        $criteriaNames = func_get_args();
        $comparer = function ($first, $second) use ($criteriaNames) {
            // Do we have anything to compare?
            while (!empty($criteriaNames)) {
                // What will we compare now?
                $criterion = array_shift($criteriaNames);

                // Used to reverse the sort order by multiplying
                // 1 = ascending, -1 = descending
                $sortOrder = 1; 
                if (is_array($criterion)) {
                    $sortOrder = $criterion[1] == SORT_DESC ? -1 : 1;
                    $criterion = $criterion[0];
                }

                // Do the actual comparison
                if ($first[$criterion] < $second[$criterion]) {
                    return -1 * $sortOrder;
                }
                else if ($first[$criterion] > $second[$criterion]) {
                    return 1 * $sortOrder;
                }

            }

            // Nothing more to compare with, so $first == $second
            return 0;
        };

        return $comparer;
    }
}
