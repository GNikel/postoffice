<?php

namespace App\Traits;

use App\Modules\PostOffice\Entity\Item\ItemAbstract;
use App\Modules\PostOffice\Entity\Postman\PostmanAbstract;

trait CountTrait
{

    private function countInner($array, $i = 0)
    {
        foreach ($array as $k) {
            $i += count($k);
        }
        return $i;
    }

    private function countInNotFullPostmans($array, $i = 0)
    {
        foreach ($array as $k) {
            if ($k->isFull()) continue;
            $i += $k->getItemCount();
        }
        return $i;
    }


}
