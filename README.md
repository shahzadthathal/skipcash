"# SkipCash Payment Gateway"

After installing pacakge please add below line  to config/app.cphp 'providers' array.

`Shahzadthathal\Skipcash\Providers\SkipcashProvider::class`


`php artisan vendor:publish --tag=config`
SET SkipCash Sandbox credentials in in your .env file.

`SKIPCASH_CLIENT_ID=''
SKIPCASH_KEY_ID=''
SKIPCASH_KEY_SECRET=''
SKIPCASH_WEBHOOK_KEY=''
SKIPCASH_URL='https://skipcashtest.azurewebsites.net'
#SKIPCASH_URL='https://api.skipcash.app'`

`php artisan vendor:publish --tag=routes`
Above command will create a skipcash.php route file.
Please include skipcash.php in the end of the web.php file.
`require __DIR__.'/skipcash.php';`

Create a new controller in i.e. SkipCashController.php and update it in skipcash.php,
Copy the content of `Shahzadthathal\Skipcash\Http\Controllers\SkipCashController` and paste into your controller.

Now you can access these routes to generate and verify payments.

`http://127.0.0.1:8000/payment/generate-payment-link`
`http://127.0.0.1:8000/payment/gateway/response/skipcash`
`http://127.0.0.1:8000/payment/gateway/response/skipcash/webhook`