# SkipCash Payment Gateway

## Install package

`composer require shahzadthathal/skipcash`

Add Skipcash Provider to config/app.php `providers` array.

`Shahzadthathal\Skipcash\Providers\SkipcashProvider::class`

Update .env file

```
SKIPCASH_CLIENT_ID=''
SKIPCASH_KEY_ID=''
SKIPCASH_KEY_SECRET=''
SKIPCASH_WEBHOOK_KEY=''
SKIPCASH_URL='https://skipcashtest.azurewebsites.net'
#SKIPCASH_URL='https://api.skipcash.app'
```

Publish config skipcash helper if you want otherwise leave this command.

`php artisan vendor:publish --tag=config`


Run migration
This will create skipcash_logs table where we will save logs from payment gateway.

`php artisan migrate`


There are two methods to use this package.

### 1. First method

You can use built in Trait in your controller:

```
use Shahzadthathal\Skipcash\Traits\SkipCashPaymentGatewayTrait;
use Shahzadthathal\Skipcash\Models\SkipcashLogs;

class YourPaymentController extends Controller{

    use SkipCashPaymentGatewayTrait;

    //Generate payment link
    //http://127.0.0.1:8000/payment/generate-payment-link
    $this->generatePaymentLinkSkipcash(Request $request){
            //Your custom transaciton id or order id
            $transactionId = 'xyz12345';
            //Sequance of the fields is important, don't move keys
            $requestData = [
                "Uid" =>\Str::uuid()->toString(),
                "KeyId"=>   env('SKIPCASH_KEY_ID'),
                "Amount" => "40.00",
                'FirstName' => 'Muhammad',
                'LastName' => 'Shahzad',
                'Phone' => '+971507520175',
                'Email' => 'shahzadthathal@gmail.com',
                "TransactionId" => $transactionId,
                "Custom1" => 'Custom1 anything',
                // Add other required fields... i.e. Custom2
            ];
            $responseSkipcashResponse = $this->generatePaymentLinkSkipcash($requestData);
            $responseSkipcashArr = json_decode($responseSkipcashResponse, true);            
            if(isset($responseSkipcashArr['resultObj'])){
                $payUrl = $responseSkipcashArr['resultObj']['payUrl'];
                header('Content-Type: application/json; charset=utf-8');
                header("Location:".$payUrl);
                exit;
            }

    }

    //Validate payment
    //http://127.0.0.1:8000/payment/gateway/response/skipcash
   //Please update above url in SkipCash payment portal in Return URL input box.
    $this->validatePaymentSkipcash(Request $request){
            $payment_id = $request->get('id');

            //Save logs
            $currentUrlWithParams = url()->full();
            $skipcashLogs = new SkipcashLogs();
            $skipcashLogs->user_id = \Auth::user()->id??0;
            $skipcashLogs->logs = 'returning '.$currentUrlWithParams;
            $skipcashLogs->save();

            //Verify payment
            $responseSkipcash = $this->validatePaymentSkipcash($payment_id);
            $responseSkipcashArr = json_decode($responseSkipcash, true);
            $resultObj = $responseSkipcashArr['resultObj'];
            //Payment success
            if(isset($resultObj['statusId']) && $resultObj['statusId']===2){
                //Your custom transaciton id or order id from the payment gateway
                $transactionId = $resultObj['transactionId'];
                dd('transactionId '.$transactionId.' is verifid payment please update your order.');
            }
    }

    //Webhook
   //http://127.0.0.1:8000/payment/gateway/response/skipcash/webhook
   //Please update above url in SkipCash payment portal in Webhook URL input box.
    public function paymentGatewayResponseSkipcashWebhook(Request $request){
        try{
            $data = $request->all();
            $skipcashLogs = new SkipcashLogs();
            $skipcashLogs->user_id = 0;
            $skipcashLogs->logs = 'webhook '.$data;
            $skipcashLogs->save();
            return response()->json(['message' => 'Success'], 200);
        }catch(\Exception $e){
            throw $e;
        }   

    }

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

