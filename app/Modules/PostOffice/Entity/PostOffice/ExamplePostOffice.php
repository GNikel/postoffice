<?php

namespace App\Modules\PostOffice\Entity\PostOffice;

use App\Modules\PostOffice\Entity\Item\ItemAbstract;
use App\Modules\PostOffice\Entity\Postman\PostmanAbstract;
use App\Traits\CheckTrait;

class ExamplePostOffice implements PostOfficeInterface
{
    use CheckTrait;

    /** @var PostmanAbstract[] */
    private array $postmen;
    /** @var ItemAbstract[] */
    private array $itemsQueue = [];

    /**
     * @param array $postmen
     */
    public function __construct(array $postmen)
    {
        $this->checkPostmen($postmen);

        $this->postmen = $postmen;
    }

    /**
     * @param array $items
     * @return PostmanAbstract[]|array
     */
    public function liveDay(array $items = []): array
    {
        $this->pushItemsInQueue($items);
        $this->fillPostmen();

        return $this->postmen;
    }

    /**
     * @return bool
     */
    public function isEmptyItemsQueue(): bool
    {
        return !count($this->itemsQueue);
    }

    /**
     * @return bool
     */
    public function isAllItemsDelivered(): bool
    {
        if (count($this->itemsQueue)) {
            return false;
        }

        foreach ($this->postmen as $postman) {
            if ($postman->hasItems()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ItemAbstract[] $items
     */
    private function pushItemsInQueue(array $items = []): void
    {
        $this->checkItems($items);

        $this->itemsQueue = array_merge($this->itemsQueue, $items);
    }

    /**
     * @return void
     */
    private function fillPostmen(): void
    {
        foreach ($this->itemsQueue as $index => $item) {
            shuffle($this->postmen);
            /** @var PostmanAbstract $postman */
            $postman = current($this->postmen);

            if (!$postman->getItemFreeSlotCount($item)) {
                continue;
            }

            $postman->putItem($item);

            unset($this->itemsQueue[$index]);
        }
    }
}
