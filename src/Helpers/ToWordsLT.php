<?php

declare(strict_types=1);

namespace App\Helpers;

class ToWordsLT
{
    public function monthLT(int $month)
    {
        switch ($month) {
            case 1:
                return 'sausio';

                break;
            case 2:
                return 'vasario';

                break;
            case 3:
                return 'kovo';

                break;
            case 4:
                return 'balandžio';

                break;
            case 5:
                return 'gegužės';

                break;
            case 6:
                return 'birželio';

                break;
            case 7:
                return 'liepos';

                break;
            case 8:
                return 'rugpjūčio';

                break;
            case 9:
                return 'rugsėjo';

                break;
            case 10:
                return 'spalio';

                break;
            case 11:
                return 'lapkričio';

                break;
            case 12:
                return 'gruodžio';

                break;
        }
    }

    public function toWordsLT($l): string
    {
        $sk1000[0] = 'tūkstantis ';
        $sk1000[1] = 'tūkstančiai ';
        $sk1000[2] = 'tūkstančių ';

        $sk1000000[0] = 'milijonas ';
        $sk1000000[1] = 'milijonai ';
        $sk1000000[2] = 'milijonų ';

        $minus = 'minus ';
        $skaiz = '';

        if ($l < 0) {
            $skaiz = $skaiz.$minus;
            $l1 = $l * (-1);
        } else {
            $l1 = $l;
        }

        // skaidom skaiciu
        $lv = $l1;

        settype($lv, 'integer');
        for ($i = 8; $i >= 0; --$i) {
            $ls = (int) ($lv / 10);
            $ll = $lv % 10;
            $ld[$i] = $ll;
            $lv = $ls;
        }
        // rasom zodzius
        $skaiz = $skaiz.$this->simtai($ld[0], $ld[1], $ld[2]);

        // milijonai
        if (1 != $ld[1] and 1 == $ld[2]) {
            $skaiz = $skaiz.$sk1000000[0];
        }
        if (1 != $ld[1] and 1 != $ld[2] and 0 != $ld[2]) {
            $skaiz = $skaiz.$sk1000000[1];
        }
        if (1 == $ld[1] and 0 != $ld[2]) {
            $skaiz = $skaiz.$sk1000000[2];
        }
        if ((0 != $ld[0] or 0 != $ld[1]) and 0 == $ld[2]) {
            $skaiz = $skaiz.$sk1000000[2];
        }

        $skaiz = $skaiz.$this->simtai($ld[3], $ld[4], $ld[5]);

        // tukstanciai
        if (1 != $ld[4] and 1 == $ld[5]) {
            $skaiz = $skaiz.$sk1000[0];
        }
        if (1 != $ld[4] and 1 != $ld[5] and 0 != $ld[5]) {
            $skaiz = $skaiz.$sk1000[1];
        }
        if (1 == $ld[4] and 0 != $ld[5]) {
            $skaiz = $skaiz.$sk1000[2];
        }
        if ((0 != $ld[3] or 0 != $ld[4]) and 0 == $ld[5]) {
            $skaiz = $skaiz.$sk1000[2];
        }

        return ucfirst ($skaiz.$this->simtai($ld[6], $ld[7], $ld[8]));
    }

    private function simtai($s, $d, $v)
    {
        $sk1[0] = 'vienas ';
        $sk1[1] = 'du ';
        $sk1[2] = 'trys ';
        $sk1[3] = 'keturi ';
        $sk1[4] = 'penki ';
        $sk1[5] = 'šeši ';
        $sk1[6] = 'septyni ';
        $sk1[7] = 'aštuoni ';
        $sk1[8] = 'devyni ';
        $sk1[9] = 'dešimt ';
        $sk11[0] = 'vienuolika ';
        $sk11[1] = 'dvylika ';
        $sk11[2] = 'trylika ';
        $sk11[3] = 'keturiolika ';
        $sk11[4] = 'penkiolika ';
        $sk11[5] = 'šešiolika ';
        $sk11[6] = 'septyniolika ';
        $sk11[7] = 'aštuoniolika ';
        $sk11[8] = 'devyniolika ';
        $sk11[9] = 'dvidešimt ';
        $sk10[0] = 'dešimt ';
        $sk10[1] = 'dvidešimt ';
        $sk10[2] = 'trisdešimt ';
        $sk10[3] = 'keturiasdešimt ';
        $sk10[4] = 'penkiasdešimt ';
        $sk10[5] = 'šešiasdešimt ';
        $sk10[6] = 'septyniasdešimt ';
        $sk10[7] = 'aštuoniasdešimt ';
        $sk10[8] = 'devyniasdešimt ';
        $sk10[9] = 'šimtas ';
        $sk100[0] = 'šimtas ';
        $sk100[1] = 'šimtai ';

        $simtz = '';

        if (0 != $s) {
            $simtz = $simtz.$sk1[$s - 1];
            if (1 == $s) {
                $simtz = $simtz.$sk100[0];
            } else {
                $simtz = $simtz.$sk100[1];
            }
        }

        if (0 != $d) {
            if (1 != $d or 0 == $v) {
                $simtz = $simtz.$sk10[$d - 1];
            }
        }

        if (0 != $v) {
            if (1 == $d) {
                $simtz = $simtz.$sk11[$v - 1];
            } else {
                $simtz = $simtz.$sk1[$v - 1];
            }
        }

        return $simtz;
    }
}
