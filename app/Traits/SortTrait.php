<?php

namespace App\Traits;

use App\Modules\PostOffice\Entity\Item\ItemAbstract;
use App\Modules\PostOffice\Entity\Postman\PostmanAbstract;

trait SortTrait
{
    /**
     * @param array $postmen
     * @param ItemAbstract $item
     */
    private function sortPostmen(array $postmens, ItemAbstract $item): array
    {
        usort($postmens, $this->cmp($item));
        return $postmens;
    }


    private function cmp(ItemAbstract $item)
    {
        return function (PostmanAbstract $a, PostmanAbstract $b) use ($item) {
            if ($a->getItemCount() == $b->getItemCount()) {
                return 0;
            }
            return ($a->getItemCount() > $b->getItemCount()) ? -1 : 1;
        };
    }


}
