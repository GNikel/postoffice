<?php

namespace App\Modules\PostOffice\Entity\PostOffice;

use App\Modules\PostOffice\Entity\Item\ItemAbstract;
use App\Modules\PostOffice\Entity\Postman\PostmanAbstract;
use App\Traits\CheckTrait;
use App\Traits\SortTrait;
use App\Traits\CountTrait;

class CandidatePostOffice implements PostOfficeInterface
{

    use CheckTrait, SortTrait, CountTrait;

    /** @var PostmanAbstract[] */
    private array $postmen;
    /** @var array */
    private array $itemsQueue = [];

    /**
     * @param PostmanAbstract[] $postmen
     */
    public function __construct(array $postmen)
    {
        $this->checkPostmen($postmen);
        $this->postmen = $postmen;
    }

    /**
     * Good time for filling postmen
     * @param ItemAbstract[] $items
     * @return PostmanAbstract[]
     */
    public function liveDay(array $items = []): array
    {
        $this->pushItemsInQueue($this->getNotSendedItems());

        $this->checkItems($items);

        $this->pushItemsInQueue($items);

        $this->fillPostmen();

        return $this->postmen;
    }

    /**
     * @return bool
     */
    public function isEmptyItemsQueue(): bool
    {
        return !$this->countInner($this->itemsQueue);
    }

    /**
     * @return bool
     */
    public function isAllItemsDelivered(): bool
    {
        if ($this->countInner($this->itemsQueue)) {
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
        foreach ($this->itemsQueue as $upIndex => $dailyItems) {
            if (!count($dailyItems)) unset($this->itemsQueue[$upIndex]);
        }
        foreach ($items as $item) {
            $this->itemsQueue[$item->getExpirationDay()][] = $item;
        }
        ksort($this->itemsQueue);
    }

    /**
     * @return void
     */
    private function fillPostmen(): void
    {
        foreach ($this->itemsQueue as $upIndex => $dailyItems) {
            foreach ($dailyItems as $dayIndex => $item) {
                $this->postmen = $this->sortPostmen($this->postmen, $item);
                foreach ($this->postmen as $postman) {
                    if ($postman->isFull()) continue;
                    if (!$postman->getItemFreeSlotCount($item)) {
                        continue;
                    }
                    $postman->putItem($item);
                    unset($this->itemsQueue[$upIndex][$dayIndex]);
                    break;
                }
            }
        }

        $countInPosts = $this->countInNotFullPostmans($this->postmen);
        $i = 0;
        while ($countInPosts >= 3) {
            $this->shufflePostmen();
            if ($i++ > 81) {
                break;
            }
            $countInPosts = $this->countInNotFullPostmans($this->postmen);
        }
    }

    /**
     * перемешивает доставщиков пытаясь утрамбовать в остатки мощности
     * @return void
     */
    private function shufflePostmen(): void
    {
        $notSendedItems = $this->getNotSendedItems();
        shuffle($notSendedItems);
        shuffle($this->postmen);
        foreach ($notSendedItems as $dayIndex => $item) {
            foreach ($this->postmen as $postman) {
                if ($postman->isFull()) continue;
                if (!$postman->getItemFreeSlotCount($item)) {
                    continue;
                }
                $postman->putItem($item);
                unset($notSendedItems[$dayIndex]);
                break;
            }
        }
    }

    /**
     * Освобождает разносчиков и возвращает их посылки при неполном заполнении
     * @return void
     */
    private function getNotSendedItems(): array
    {
        $notSendedItems = [];
        foreach ($this->postmen as $postman) {
            if ($postman->isFull()) continue;
            if (!$postman->hasItems()) continue;
            $notSendedItems = array_merge($postman->pullAllItems(), $notSendedItems);
        }
        return $notSendedItems;
    }

}
