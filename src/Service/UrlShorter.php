<?php

namespace App\Service;


class UrlShorter
{
    const CHARS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    public function encode($n)
    {
        $base = strlen(self::CHARS);
        $converted = '';

        while ($n > 0) {
            $converted = substr(self::CHARS, bcmod($n,$base), 1) . $converted;
            $n = $this->bcFloor(bcdiv($n, $base));
        }

        return $converted ;
    }

    public function decode($code)
    {
        $base = strlen(self::CHARS);
        $c = '0';
        for ($i = strlen($code); $i; $i--) {
            $c = \bcadd($c,\bcmul(strpos(self::CHARS, substr($code, (-1 * ( $i - strlen($code) )),1))
                ,\bcpow($base,$i-1)));
        }

        return \bcmul($c, 1, 0);
    }

    private function bcFloor($x)
    {
        return \bcmul($x, '1', 0);
    }
}