<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/4/20
 */

namespace App\Imports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;


class UsersImport implements ToArray
{
    public function Array(Array $tables)
    {
        return $tables;
    }

}