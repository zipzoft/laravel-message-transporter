## Laravel Message Transporter

เป็นตัว broadcast ข้อมูลไปยัง service ต่างๆ (แนวคิด microservice)


### สิ่งที่ต้องการ
```json
{
  "php": "^7.4|^8.0"
}
```


### ติดตั้ง
```
composer require zipzoft/laravel-message-transporter
```

### ตั้งค่า
โดยปกติแล้ว `env` จะมีค่าเริ่มต้นคือ
```
SERVICE_BROADCASTER_DRIVER=none
```

### Driver
ตอนนี้ใช้ได้แค่ redis นะครับ
```
SERVICE_BROADCASTER_DRIVER=redis 
```



---


## การตั้งค่า

นี่คือค่า Default
```php
// /config/message-transporter.php

return [
    'default' => env('SERVICE_BROADCASTER_DRIVER', 'none'),
    'connection_prefix' => 'app-services_',
    'queue' => true,
]
```



หรือแก้ไขได้เอง โดยรันคำสั่งด้านล่าง

Config จะวางอยุ่ที่ /config/message-transporter.php
```
php artisan vendor:publish --class="Zipzoft\MessageTransporter\MessageTransporterServiceProvider" --tag="config"
```

---

## เริ่มใช้งาน

### ส่วนของ Producer

ตัวอย่าง config ของ Producer
```php
// /config/database.php

return [
    // ...
    
    'redis' => [
        // ....
        
        'app-services_producer' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => 2,
            'options' => [
                'prefix' => env('SERVICE_BROADCASTER_PREFIX'),
            ]
        ]
    ]
];
```


ใส่ implementation ไปที่ Event ของท่าน

ซึ่งรูปแบบการใช้งานนั้น ท่านสามารถอ้างอิงได้จากเอกสารของ [Laravel](https://laravel.com/docs/broadcasting) ได้เลย

```php
<?php

namespace App\Events;

use App\User;
use Zipzoft\MessageTransporter\ShouldBroadcastAppServices;
use Zipzoft\MessageTransporter\ShouldBroadcastAppServicesNow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class UserCreated implements ShouldBroadcastAppServices
{
    // ....
    
    public $user;
    
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function broadcastOn()
    {
        return [
            new Channel("app"),
            new PrivateChannel("call-center"),
        ];
    }
    
    
    // Supported method
    // broadcastWith, broadcastWhen, broadcastAs
}
```


โดยปกแล้วจะส่งโดยใช้ Queue เป็นหลัก

หากท่านไม่ต้องการส่งแบบ queue ให้ท่านใช้ ShouldBroadcastAppServicesNow แทนได้เลย
```php
use Zipzoft\MessageTransporter\ShouldBroadcastAppServicesNow;
```


Format ที่ส่งไปนั้นอยู่ในรูปแบบ JSON

```json
{
  "event" : "App\\Events\\UserCreated",
  "data" : {
    "user" : {
      "id" : "....",
      "name" : "..."
    }
  },
  "sent" : "2021-09-10T15:13:12+07:00"
}
```
ส่วน Channel name นั้น จะอ่านจาก method `broadcastOn` เป็นหลัก
ดังนั้น ในที่นี้ชื่อจะเป็นตามต้วอย่างด้านล่าง
```
app
private-call-center
```

เมื่อรวมกัน prefix ที่ท่านตั้งค่าใน `env('SERVICE_BROADCASTER_PREFIX')` แล้ว `(สมมติ SERVICE_BROADCASTER_PREFIX = 'myweb::')`
จะได้
```
myweb::app
myweb::private-call-center
```
--- 

### ส่วนของ Consumer


ตัวอย่าง config ของ consumer
```php
// /config/database.php

return [
    // ...
    
    'redis' => [
        // ....
        
        'app-services_consumer' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => 2,
            'options' => [
                'prefix' => '',
            ]
        ]
    ]
];
```


คุณจำเป็นต้องสร้าง Artisan Command มาก่อนเมื่อเป็นตัวรับข้อมูลตลอดเวลา เช่นในตัวอย่างนี้

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Zipzoft\MessageTransporter\Broadcasters\ServiceBroadcaster;
use Zipzoft\MessageTransporter\Event\OnMessage;

class SubscribeAppServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe other services';

    /**
     * @var ServiceBroadcaster 
     */
    private ServiceBroadcaster $broadcaster;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ServiceBroadcaster $broadcaster)
    {
        parent::__construct();
        
        $this->broadcaster = $broadcaster;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->broadcaster->subscribe(['myweb*'], function (OnMessage $event) {
            // $event->event;
            // $event->message;
            // $event->channel;
        });

        return 0;
    }
}
```

