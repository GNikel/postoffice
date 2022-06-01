<?php

namespace App\Traits;

use App\Modules\PostOffice\Entity\Item\ItemAbstract;
use App\Modules\PostOffice\Entity\Postman\PostmanAbstract;
use LogicException;

trait CheckTrait
{
    /**
     * @param array $postmen
     */
    private function checkPostmen(array $postmen): void
    {
        foreach ($postmen as $postman) {
            if (!($postman instanceof PostmanAbstract)) {
                throw new LogicException(
                    sprintf('Postman must be an instance of PostmanAbstract class. %s given.', get_class($postman))
                );
            }
        }
    }


    /**
     * @param array $items
     */
    private function checkItems(array $items): void
    {
        foreach ($items as $item) {
            if (!($item instanceof ItemAbstract)) {
                throw new LogicException(
                    sprintf('Item must be an instance of ItemAbstract class. %s given.', get_class($item))
                );
            }
        }
    }
}
