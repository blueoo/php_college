<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2018/12/17
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @description: 增加一些通用的方法
 * Class ApiController
 * @package app\Http\Controllers\Api
 * @author zouhuaqiu
 * @date 2018/12/17
 */
class HelloController extends Controller
{
    public function world(Request $request)
    {
        $params = $request->all();
        $request->session()->setId($params['sid']);
        var_dump($request->session()->all());

    }


}