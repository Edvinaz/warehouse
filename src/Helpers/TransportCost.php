<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entity\Purchases\WarePurchasedMaterials;

class TransportCost
{
    protected $allCosts;
    protected $costsList;

    public function __construct()
    {
        $this->costsList = [];
    }

    public function getAllCosts()
    {
        return $this->allCosts;
    }

    public function setSum($sum)
    {
        $this->allCosts = $sum;
    }

    public function test()
    {
        $this->allCosts = 1000;
        $this->costsList[18969] = [
            'name' => 'Remontas',
            'price' => 100,
            'quantity' => 10,
        ];
        $this->costsList[19999] = [
            'name' => 'Techninė apžiūra',
            'price' => 100,
            'quantity' => 100,
        ];
    }

    /**
     * Koreguojamos išlaidos.
     */
    public function updateCosts(WarePurchasedMaterials $wpm)
    {
        if (\array_key_exists($wpm->getId(), $this->costsList)) {
            $old = $this->costsList[$wpm->getId()];
            $this->costsList[$wpm->getId()] = [
                'name' => (string) $wpm->getMaterial(),
                'price' => $wpm->getPrice(),
                'quantity' => $wpm->getQuantity(),
            ];

            $this->allCosts -= $old['price'] * $old['quantity'];
            $this->allCosts += $wpm->getSum();
        } else {
            $this->costsList[$wpm->getId()] = [
                'name' => (string) $wpm->getMaterial(),
                'price' => $wpm->getPrice(),
                'quantity' => $wpm->getQuantity(),
            ];
            $this->allCosts += $wpm->getSum();
        }
    }

    public function deleteCosts(WarePurchasedMaterials $wpm)
    {
        $this->allCosts -= $wpm->getSum();
        unset($this->costsList[$wpm->getId()]);

        return true;
    }
}
