<?php
declare(strict_types=1);

namespace App\Services\Purchase;

use App\Settings\Settings;
use App\Traits\IteratorTrait;
use App\Services\PurchaseService;
use App\Helpers\Iterators\ItemsCollection;

class InvoiceListService extends PurchaseService
{
    use IteratorTrait;

    private $list;

    public function getList(string $search = '', int $page = 0)
    {
        $this->setList($search);

        $list = new ItemsCollection();

        if (empty($search)) {
            return $this->iterate($page);
        }

        $chunk = [];

        foreach ($this->list as $item) {
            if (false !== strpos(strtolower($item->getContrahent()->getName()), strtolower($search))) {
                $chunk[] = $item;
            } else if (false !== strpos(strtolower($item->getNumber()), strtolower($search))) {
                $chunk[] = $item;
            }
            if (count($chunk) === Settings::ITEMS) {
                $list->addItem($chunk);
                $chunk = [];
            }
        }
        if (count($chunk) > 0) {
            $list->addItem($chunk);
        }

        return $list->getIterator();
    }

    private function setList(string $search = '')
    {
        $this->list = $this->invoicesRepository->invoiceList($search);
// dd($this->list);
        return $this;
    }
}