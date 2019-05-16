# php_college
## 基于laravel 5.5 下实际业务场景中一些组件的封装和示例
+ rdkafka的封装和使用示例
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

  

