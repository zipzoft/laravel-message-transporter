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



### เริ่มใช้งาน
ใส่ implementation ไปที่ Event ของท่าน

ซึ่งรูปแบบการใช้งานนั้น ท่านสามารถอ้างอิงได้จากเอกสารของ [Laravel](https://laravel.com/docs/broadcasting) ได้เลย

```php
<?php

namespace App\Events;

use Zipzoft\MessageTransporter\ShouldBroadcastAppServices;
use Zipzoft\MessageTransporter\ShouldBroadcastAppServicesNow;
use Illuminate\Broadcasting\Channel;

class UserCreated implements ShouldBroadcastAppServices
{
    // ....
    
    public function broadcastOn()
    {
        return [
            new Channel("app"),
        ];
    }
    
    
    // Supported method
    // broadcastWith, broadcastWhen, broadcastAs
}
```


โดยปกแล้วจะส่งโดยใช้ Queue เป็นหลัก

หากท่านไม่ต้องการส่งแบบ queue ให้ท่านใช้ ShouldBroadcastAppServicesNow ได้เลย
```php
use Zipzoft\MessageTransporter\ShouldBroadcastAppServicesNow;
``` 

เช่น
```php
<?php

namespace App\Events;

use Zipzoft\MessageTransporter\ShouldBroadcastAppServices;
use Zipzoft\MessageTransporter\ShouldBroadcastAppServicesNow;
use Illuminate\Broadcasting\Channel;

class UserCreated implements ShouldBroadcastAppServices
{
    //
}
```




### การตั้งค่า

นี่คือค่า Default
```php
// config path: message-transporter
return [
    'default' => env('SERVICE_BROADCASTER_DRIVER', 'none'),
    'connection_prefix' => 'app-services_',
    'queue' => false,
]
```


หรือแก้ไขได้เอง โดยรันคำสั่งด้านล่าง

Config จะวางอยุ่ที่ /config/message-transporter.php
```
php artisan vendor:publish --class="Zipzoft\MessageTransporter\MessageTransporterServiceProvider" --tag="config"
```
