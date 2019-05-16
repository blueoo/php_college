<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2018/12/25
 */

namespace App\Libraries\ConsistentHash;


class ConsistentHashFactory
{
    protected static $hashFactory = [];

    public static function getHashFactory($num,$nodePrefix='')
    {

        if (empty($hashFactory[$nodePrefix])) {
            $hash = new ConsistentHash();
            $target = [];
            for ($i = 0; $i < $num; $i++) {
                $target[] = $nodePrefix . $i;
            }
            $hash->addTargets($target);
            $hashFactory[$nodePrefix] = $hash;

        }
        return $hashFactory[$nodePrefix];

    }


}