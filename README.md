# SkipCash Payment Gateway

## Install package

`composer require shahzadthathal/skipcash`

Add Skipcash Provider to config/app.php `providers` array.

`Shahzadthathal\Skipcash\Providers\SkipcashProvider::class`

Create config file.

`php artisan vendor:publish --tag=config`

Update .env file

```
SKIPCASH_CLIENT_ID=''
SKIPCASH_KEY_ID=''
SKIPCASH_KEY_SECRET=''
SKIPCASH_WEBHOOK_KEY=''
SKIPCASH_URL='https://skipcashtest.azurewebsites.net'
#SKIPCASH_URL='https://api.skipcash.app'
```

Run migration

`php artisan migrate`


There are two methods to use this package.

### 1. First method

You can use built in Trait in your controller:

```
use Shahzadthathal\Skipcash\Traits\SkipCashPaymentGatewayTrait;

class YourPaymentController extends Controller{

    use SkipCashPaymentGatewayTrait;

    $this->generatePaymentLinkSkipcash(...)

    $this->validatePaymentSkipcash(...)

}
```
Please see `\vendor\shahzadthathal\skipcash\src\Http\Controllers\SkipCashController.php` for more methods.

### 2. Second method

`php artisan vendor:publish --tag=routes`

Above command will create a skipcash.php route file.
Please include skipcash.php in the end of the web.php file.

`require __DIR__.'/skipcash.php';`


Create a new controller i.e. YourSkipCashController.php and update it in skipcash.php route file. Copy the content of `\vendor\shahzadthathal\skipcash\src\Http\Controllers\SkipCashController.php` and paste into your controller.

Now you can access these routes to generate pay link and verify payments.

```
http://127.0.0.1:8000/payment/generate-payment-link
http://127.0.0.1:8000/payment/gateway/response/skipcash
http://127.0.0.1:8000/payment/gateway/response/skipcash/webhook
```

