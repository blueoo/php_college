<?php
/**
 * @description: 记录service
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2018/12/18
 */

namespace App\Services\LogService;

use Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\AsyncLogJob;

class LogService
{
    /**
     * @description: 异步记录log方法
     * @param $data
     * @param string $col_name
     * @param int $type
     * @param array $option
     * @author zouhuaqiu
     * @date 2018/12/24
     */
    public static function asyncLog($data, $col_name = '', $type = 3, $option = array())
    {
        //获得引用的file 和 line
        $level = (isset($option['level']) && $option['level'] > 0) ? intval($option['level']) : 0;
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $method = !empty(Request::method()) ? Request::method() : '';
        $uri = !empty(Request::path()) ? Request::path() : '';
        $client = !empty(Request::ip()) ? Request::ip() : '';
        try {
            $controller = self::getCurrentAction()['controller'];
            $function = self::getCurrentAction()['method'];
        } catch (\Exception $e) {
            $controller = '';
            $function = '';
        }
        //保存日志
        $insert = array(
            'create_time' => time(),
            'create_date' => date('Y-m-d H:i:s'),
            'client' => $client,
            'uri' => $uri,
            'file' => isset($trace[$level]['file']) ? $trace[$level]['file'] : '',
            'line' => isset($trace[$level]['line']) ? $trace[$level]['line'] : '',
            'method' => $method,
            'controller' => !empty($controller) ? $controller : '',
            'function' => !empty($function) ? $function : '',
            'type' => intval($type),
            'data' => $data
        );
        // 合并其他字段
        if (isset($option['extra']) && is_array($option['extra']) && !empty($option['extra'])) {
            $insert = array_merge($insert, $option['extra']);
        }
        if (empty($col_name)) {

            $col_name = 'log_' . date('Ymd');
        }
        $save = array(
            'insert' => $insert,
            'col_name' => $col_name,
        );
        // 分开到异步任务的队列
        AsyncLogJob::dispatch($save)->onQueue(config('jobqueue.list.LOG_JOB'));
    }

    /**
     * @description: 同步记录方法
     * @param $data 要记录的数据
     * @param string $col_name 集合名
     * @param int $type
     * @param array $option
     * @return mixed
     * @author zouhuaqiu
     * @date 2018/12/20
     */
    public static function mongoLog($data, $col_name = '', $type = 3, $option = array())
    {
        //获得引用的file 和 line
        $level = (isset($option['level']) && $option['level'] > 0) ? intval($option['level']) : 0;
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $method = !empty(Request::method()) ? Request::method() : '';
        $uri = !empty(Request::path()) ? Request::path() : '';
        $client = !empty(Request::ip()) ? Request::ip() : '';
        try {
            $controller = self::getCurrentAction()['controller'];
            $function = self::getCurrentAction()['method'];
        } catch (\Exception $e) {
            $controller = '';
            $function = '';
        }
        //保存日志
        $insert = array(
            'create_time' => time(),
            'create_date' => date('Y-m-d H:i:s'),
            'client' => $client,
            'uri' => $uri,
            'file' => isset($trace[$level]['file']) ? $trace[$level]['file'] : '',
            'line' => isset($trace[$level]['line']) ? $trace[$level]['line'] : '',
            'method' => $method,
            'controller' => !empty($controller) ? $controller : '',
            'function' => !empty($function) ? $function : '',
            'type' => intval($type),
            'data' => $data
        );
        // 合并其他字段
        if (isset($option['extra']) && is_array($option['extra']) && !empty($option['extra'])) {
            $insert = array_merge($insert, $option['extra']);
        }
        if (empty($col_name)) {

            $col_name = 'log_' . date('Ymd');
        }

        $connection = self::connectionMongodb($col_name);
        $result = $connection->insert($insert);

        return $result;
    }



    /**
     * @description: 获取mongodb的
     * @param $tables
     * @return mixed
     * @author zouhuaqiu
     * @date 2018/12/20
     */
    public static function connectionMongodb($tables)
    {
        return $users = DB::connection('mongodb')->collection($tables);
    }

    /**
     * 获取当前控制器与方法
     *
     * @return array
     */
    public static function getCurrentAction()
    {
        $route = \Route::current();
        if (empty($route)) {
            return ['controller' => '', 'method' => ''];
        }
        $action = \Route::current()->getActionName();
        list($class, $method) = explode('@', $action);
        return ['controller' => $class, 'method' => $method];
    }


}