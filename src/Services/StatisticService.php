<?php

declare(strict_types=1);

namespace App\Services;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class StatisticService
{
    private $em;
    private $session;
    private $period;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
        $this->session = new Session();
        $this->period = [];
    }

    public function getMonthReport()
    {
        $date = $this->session->get('interval')->getDate();
        $conn = $this->em->getConnection();
        $sql = '
            SELECT 
                wm.name, wm.unit, wmc.type, 
                wpm.amount as begin_amount, 
                wpm.purchased_price as begin_price, 
                wpm.purchased_sum as begin_sum, 
                wpm2.purchase_quantity as got_amount,
                wpm2.purchase_price as got_price,
                wpm2.purchased_sum as got_sum, 
                COALESCE(db.debited_amount, 0) as debited_amount,
                COALESCE(db.debited_price, 0) as debited_price,
                COALESCE(db.debited_sum, 0) as debited_sum,
                COALESCE(wpm.amount, 0) + COALESCE(wpm2.purchase_quantity, 0) - COALESCE(db.debited_amount, 0) as left_amount,
                (COALESCE(wpm.purchased_sum, 0) + COALESCE(wpm2.purchased_sum, 0) - COALESCE(db.debited_sum, 0))/(COALESCE(wpm.amount, 0) + COALESCE(wpm2.purchase_quantity, 0) - COALESCE(db.debited_amount, 0)) as left_price,
                COALESCE(wpm.purchased_sum, 0) + COALESCE(wpm2.purchased_sum, 0) - COALESCE(db.debited_sum, 0) as left_sum
            FROM `ware_materials` wm
            JOIN ware_material_categories wmc ON wmc.id=wm.category_id

            LEFT JOIN (
                SELECT 
                    material_id, 
                    SUM(price * (quantity - COALESCE(debited_amount, 0))) as purchased_sum, 
                    SUM(price * (quantity - COALESCE(debited_amount, 0)))/SUM(quantity - COALESCE(debited_amount, 0)) as purchased_price,
                    SUM(quantity - COALESCE(debited_amount, 0)) as amount 
                FROM ware_purchased_materials wpm
                JOIN ware_invoices wi ON wi.id=wpm.invoice_id
                LEFT JOIN (
                    SELECT SUM(wdm.amount) as debited_amount, purchase_id FROM ware_debited_materials wdm
                    JOIN ware_write_offs wfo ON wfo.id=wdm.writeoff_id
                    WHERE wfo.date<:from
                    GROUP BY purchase_id
                ) wdm ON wdm.purchase_id=wpm.id
                WHERE date < :from
                GROUP BY material_id
            ) wpm ON wpm.material_id=wm.id

            LEFT JOIN (
                SELECT 
                    material_id, 
                    SUM(price * quantity) as purchased_sum,
                    SUM(quantity) as purchase_quantity,
                    SUM(price * quantity)/SUM(quantity) as purchase_price
                FROM ware_purchased_materials wpm
                JOIN ware_invoices wi ON wi.id=wpm.invoice_id
                WHERE date BETWEEN :from AND :to
                GROUP BY material_id
            ) wpm2 ON wpm2.material_id=wm.id

            LEFT JOIN (
                SELECT 
                    COALESCE(SUM(wdm.amount * wpm.price), 0) as debited_sum, 
                    COALESCE(SUM(wdm.amount * wpm.price)/SUM(wdm.amount), 0) as debited_price, 
                    SUM(wdm.amount) as debited_amount, 
                    wpm.material_id 
                FROM ware_debited_materials wdm
                JOIN ware_purchased_materials wpm ON wpm.id=wdm.purchase_id
                JOIN ware_materials wm ON wm.id=wpm.material_id
                JOIN ware_write_offs wfo ON wfo.id=wdm.writeoff_id
                WHERE wfo.date BETWEEN :from AND :to
                GROUP BY material_id
            ) db ON db.material_id=wm.id

            WHERE wmc.type<9 AND wpm.amount>0 OR wmc.type<9 AND wpm2.purchase_quantity>0
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['from' => $date->format('Y-m-01'), 'to' => $date->format('Y-m-t')]);

        return $stmt->fetchAll();
    }

    public function getPeriodStatistic()
    {
        $month = $this->getMonthOverview($this->session->get('interval')->getDate())[0];
        $this->period['begin'] = $month['begin'];
        $this->period['received'] = $month['received'];
        $this->period['debited'] = $month['debited'];
        $this->period['end'] = $month['end'];

        return $this;
    }

    /**
     * Get the value of period.
     */
    public function getPeriod()
    {
        return $this->period;
    }

    public function warehouseOverview(int $month)
    {
        $dateStatistic = [];
        $date = new DateTime();

        for ($i = 0; $i < $month; ++$i) {
            if ($i > 0) {
                $date->modify('-1 month');
            }
            $dateStatistic[$date->format('Y-m')] = $this->getMonthOverview($date);
        }

        return $dateStatistic;
    }

    /**
     * TODO metų objektų ataskaita.
     */
    public function getObjectStatistic()
    {
        $sql = '
            SELECT bi.*, SUM(bi.total) as total FROM buh_invoices as bi 
            JOIN ware_objects as wo
            WHERE bi.date BETWEEN :from AND :to
            GROUP BY bi.object_id';
        $conn = $this->em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute(['from' => '2019-01-01', 'to' => '2019-12-31']);
        dd($stmt->fetchAll());
    }

    private function getMonthOverview(DateTime $date)
    {
        $conn = $this->em->getConnection();
        $sql = '
            SELECT  
                SUM(COALESCE(wpm.purchased_sum, 0)) as begin, 
                SUM(COALESCE(wpm2.purchased_sum, 0)) as received, 
                SUM(COALESCE(db.debited_sum, 0)) as debited,
                (SUM(COALESCE(wpm.purchased_sum, 0)) + SUM(COALESCE(wpm2.purchased_sum, 0)) - SUM(COALESCE(db.debited_sum, 0))) as end
            FROM `ware_materials` wm
            JOIN ware_material_categories wmc ON wmc.id=wm.category_id

            LEFT JOIN (
                SELECT 
                    material_id, 
                    SUM(price * (quantity - COALESCE(debited_amount, 0))) as purchased_sum, 
                    SUM(price * (quantity - COALESCE(debited_amount, 0)))/SUM(quantity - COALESCE(debited_amount, 0)) as purchased_price,
                    SUM(quantity - COALESCE(debited_amount, 0)) as amount 
                FROM ware_purchased_materials wpm
                JOIN ware_invoices wi ON wi.id=wpm.invoice_id
                LEFT JOIN (
                    SELECT SUM(wdm.amount) as debited_amount, purchase_id FROM ware_debited_materials wdm
                    JOIN ware_write_offs wfo ON wfo.id=wdm.writeoff_id
                    WHERE wfo.date<:from
                    GROUP BY purchase_id
                ) wdm ON wdm.purchase_id=wpm.id
                WHERE date < :from
                GROUP BY material_id
            ) wpm ON wpm.material_id=wm.id

            LEFT JOIN (
                SELECT 
                    material_id, 
                    SUM(price * quantity) as purchased_sum,
                    SUM(quantity) as purchase_quantity,
                    SUM(price * quantity)/SUM(quantity) as purchase_price
                FROM ware_purchased_materials wpm
                JOIN ware_invoices wi ON wi.id=wpm.invoice_id
                WHERE date BETWEEN :from AND :to
                GROUP BY material_id
            ) wpm2 ON wpm2.material_id=wm.id

            LEFT JOIN (
                SELECT 
                    SUM(wdm.amount * wpm.price) as debited_sum, 
                    COALESCE(SUM(wdm.amount * wpm.price)/SUM(wdm.amount), 0) as debited_price, 
                    SUM(wdm.amount) as debited_amount, 
                    wpm.material_id 
                FROM ware_debited_materials wdm
                JOIN ware_purchased_materials wpm ON wpm.id=wdm.purchase_id
                JOIN ware_materials wm ON wm.id=wpm.material_id
                JOIN ware_write_offs wfo ON wfo.id=wdm.writeoff_id
                WHERE wfo.date BETWEEN :from AND :to
                GROUP BY material_id
            ) db ON db.material_id=wm.id

            WHERE wmc.type<9 AND wpm.amount>0 OR wmc.type<9 AND wpm2.purchase_quantity>0
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['from' => $date->format('Y-m-01'), 'to' => $date->format('Y-m-t')]);

        return $stmt->fetchAll();
    }

    /**
     * Returns fuel reminder for current month
     *
     * @return void
     */
    public function getMonthFuel()
    {
        $date = $this->session->get('interval')->getDate();
        $conn = $this->em->getConnection();
        $sql = '
            SELECT 
                wm.name, wm.unit, wmc.type, wmc.id,
                wpm.amount as begin_amount, 
                wpm.purchased_price as begin_price, 
                wpm.purchased_sum as begin_sum, 
                wpm2.purchase_quantity as got_amount,
                wpm2.purchase_price as got_price,
                wpm2.purchased_sum as got_sum, 
                COALESCE(db.debited_amount, 0) as debited_amount,
                COALESCE(db.debited_price, 0) as debited_price,
                COALESCE(db.debited_sum, 0) as debited_sum,
                COALESCE(wpm.amount, 0) + COALESCE(wpm2.purchase_quantity, 0) - COALESCE(db.debited_amount, 0) as left_amount,
                (COALESCE(wpm.purchased_sum, 0) + COALESCE(wpm2.purchased_sum, 0) - COALESCE(db.debited_sum, 0))/(COALESCE(wpm.amount, 0) + COALESCE(wpm2.purchase_quantity, 0) - COALESCE(db.debited_amount, 0)) as left_price,
                COALESCE(wpm.purchased_sum, 0) + COALESCE(wpm2.purchased_sum, 0) - COALESCE(db.debited_sum, 0) as left_sum
            FROM `ware_materials` wm
            JOIN ware_material_categories wmc ON wmc.id=wm.category_id

            LEFT JOIN (
                SELECT 
                    material_id, 
                    SUM(price * (quantity - COALESCE(debited_amount, 0))) as purchased_sum, 
                    SUM(price * (quantity - COALESCE(debited_amount, 0)))/SUM(quantity - COALESCE(debited_amount, 0)) as purchased_price,
                    SUM(quantity - COALESCE(debited_amount, 0)) as amount 
                FROM ware_purchased_materials wpm
                JOIN ware_invoices wi ON wi.id=wpm.invoice_id
                LEFT JOIN (
                    SELECT SUM(wdm.amount) as debited_amount, purchase_id FROM ware_debited_materials wdm
                    JOIN ware_write_offs wfo ON wfo.id=wdm.writeoff_id
                    WHERE wfo.date<:from
                    GROUP BY purchase_id
                ) wdm ON wdm.purchase_id=wpm.id
                WHERE date < :from
                GROUP BY material_id
            ) wpm ON wpm.material_id=wm.id

            LEFT JOIN (
                SELECT 
                    material_id, 
                    SUM(price * quantity) as purchased_sum,
                    SUM(quantity) as purchase_quantity,
                    SUM(price * quantity)/SUM(quantity) as purchase_price
                FROM ware_purchased_materials wpm
                JOIN ware_invoices wi ON wi.id=wpm.invoice_id
                WHERE date BETWEEN :from AND :to
                GROUP BY material_id
            ) wpm2 ON wpm2.material_id=wm.id

            LEFT JOIN (
                SELECT 
                    COALESCE(SUM(wdm.amount * wpm.price), 0) as debited_sum, 
                    COALESCE(SUM(wdm.amount * wpm.price)/SUM(wdm.amount), 0) as debited_price, 
                    SUM(wdm.amount) as debited_amount, 
                    wpm.material_id 
                FROM ware_debited_materials wdm
                JOIN ware_purchased_materials wpm ON wpm.id=wdm.purchase_id
                JOIN ware_materials wm ON wm.id=wpm.material_id
                JOIN ware_write_offs wfo ON wfo.id=wdm.writeoff_id
                WHERE wfo.date BETWEEN :from AND :to
                GROUP BY material_id
            ) db ON db.material_id=wm.id

            WHERE wmc.type=2 AND wpm.amount>0 OR wmc.type=2 AND wpm2.purchase_quantity>0
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['from' => $date->format('Y-m-01'), 'to' => $date->format('Y-m-t')]);

        $fuelList = $stmt->fetchAll();
        $list = [
            21 => 0,
            22 => 0,
            23 => 0
        ];

        foreach ($fuelList as $fuel) {
            $list[$fuel['id']] += $fuel['left_amount'];
        }

        return $list;
    }
}
