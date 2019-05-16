# php_college
## 基于laravel 5.5 下实际业务场景中一些组件的封装和示例
+ **rdkafka的封装和使用示例**
 >需要安装pecl rdkafka扩展  
[rdkafka源码Github](https://github.com/arnaud-lb/php-rdkafka "Markdown")  
[rdkafka的pecl](https://pecl.php.net/package/rdkafka "Markdown")  
示例说明:  
主要代码:  
App\Services\KafkaService\KafkaProducerFactory  
App\Services\KafkaService\KafkaProducer  
App\Services\KafkaService\KafkaConsumerFactory  
App\Services\KafkaService\KafkaConsumer  
示例代码位置:  
生产者示例代码：App\Http\Controllers\Index\kafkaProducer  
消费者示例代码：App\Console\Commands\Kafka\KafkaConsumer  
使用说明:  
启动消费者：php artisan command:consumer  
访问对应的生产者路由：http://{your_host}/index/kafkaProducer  

+ **异步/同步记录到Mongodb的log服务**  
>需要安装mongodb扩展，如使用异步方法则需要加装redis扩展   
功能说明:使用mongodb作为持久化记录常用的程序信息功能。  
记录的内容包括：  
1.时间戳  
2.客户端IP  
3.uri  
4.代码源文件的路径以及行数
5.对应的controller以及function  
6.自定义的信息  
主要代码：
App\Services\LogService\LogService  
示例代码位置：  
App\Http\Controllers\Index\testLog  
使用说明:  
同步方法直接使用；  
异步方法需要先启动队列消费者，如下命令（timeout和重试可以视情况省略）：   
php artisan queue:work --timeout=60 --tries=3 --queue=log_job



  

