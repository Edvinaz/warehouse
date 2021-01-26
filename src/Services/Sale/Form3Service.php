<?php
declare(strict_types=1);

namespace App\Services\Sale;

use App\Models\Form3;
use App\Services\InvoiceService;

class Form3Service extends InvoiceService
{
    public function printForm3(int $id)
    {
        $form3 = new Form3($this->printInvoice($id));
        return $form3;
    }
}