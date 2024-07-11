# MechtaMarket PHP SMS Gateway

## Введение

MechtaMarket SMS Gateway — это пакет для отправки SMS сообщений через настраиваемый шлюз для PHP проектов. 
Пакет разработан так, чтобы его можно было повторно использовать в различных проектах.

## Установка

Установите пакет с помощью Composer:

```bash
composer require mechta-market/php-sms-gateway
```

## Использование

### Инициализация сервиса

Вы можете инициализировать SmsGatewayService, предоставив базовый URL вашего SMS-шлюза:

```php
use MechtaMarket\SmsGateway\SmsGatewayService;

$base_url = 'https://sms-gateway.example.com';
$sms_gateway_service = new SmsGatewayService($base_url);
```

## Отправка SMS

Пакет предоставляет два способа отправки SMS: синхронно и асинхронно.

### Синхронная отправка

Для синхронной отправки SMS используйте метод sendSync:

```php
try {
    $sms_id = $sms_gateway_service->sendSync('1234567890', 'Тестовое сообщение');
    echo "SMS успешно отправлено с ID: $sms_id";
} catch (\Exception $e) {
    echo "Не удалось отправить SMS: " . $e->getMessage();
}
```

### Асинхронная отправка

Для асинхронной отправки SMS используйте метод sendAsync:

```php
try {
    $sms_gateway_service->sendAsync('1234567890', 'Тестовое сообщение');
    echo "SMS успешно отправлено";
} catch (\Exception $e) {
    echo "Не удалось отправить SMS: " . $e->getMessage();
}
```