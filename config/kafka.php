<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/15
 */

return [
    'connections'=>[
        'default'=>[
            'brokers' =>env('KAFKA_BROKERS', ''),
            'logLevel'=>env('KAFKA_LOG_LEVEL', LOG_DEBUG),
        ]
    ],

];