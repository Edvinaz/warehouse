<?php
declare(strict_types=1);

namespace App\Services\Transport;

use App\Entity\Purchases\WarePurchasedMaterials;
use App\Services\PurchaseService;

class FuelImportService extends PurchaseService
{
    /**
     * Import fuel from Circle K invoice details csv separated with ";"
     *
     * @param [type] $fp
     * @return void
     */
    public function importFuel($fp)
    {
        /*
         *  Circle K importas
         *  Kaina su nuolaida: $row[15]/$row[9]
         */
        $wm = $this->materialRepository->findAll();
        $wi = $this->invoiceRepository->findAll();

        while ($row = fgetcsv($fp, 1000, ';')) {
            if (is_numeric($row[1])) {
                $xx = \str_replace('  ', ' ', $row[8]);
                $ww = \array_search($xx, $wm);
                $inv = \array_search($row[17], $wi);
                $wpm = new WarePurchasedMaterials();
                $wpm->setMaterial($wm[$ww]);
                $wpm->setInvoice($wi[$inv]);
                $wpm->setQuantity($row[9]);
                $wpm->setBalance($row[9]);
                $wpm->setNote(',,'.$row[0]);
                $price = $row[15] / $row[9];
                $wpm->setPrice((string) $price);
                $wpm->setVat(21);

                $check = $this->purchasedMaterialsRepository
                    ->checkExistingPurchase($wpm);
                if (empty($check)) {
                    $this->purchasedMaterialsRepository->persist($wpm);
                }
            }
        }
        $this->purchasedMaterialsRepository->save();
    }
}