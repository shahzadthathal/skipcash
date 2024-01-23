<?php

namespace Shahzadthathal\Skipcash\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Shahzadthathal\Skipcash\Traits\SkipCashPaymentGatewayTrait;
use Shahzadthathal\Skipcash\Models\SkipcashLogs;

class SkipCashController extends Controller {

    use SkipCashPaymentGatewayTrait;

    public function generatePaymentLink(Request $request)
    {
        try{
            //Your custom transaciton id or order id
            $transactionId = 'xyz12345';
            //Sequance of the fields is important, don't move keys
            $requestData = [
                "Uid" =>\Str::uuid()->toString(),
                "KeyId"=>config('skipcash.key_id'),
                "Amount" => "40.00",
                'FirstName' => 'Muhammad',
                'LastName' => 'Shahzad',
                'Phone' => '+974507520175',
                'Email' => 'shahzadthathal@gmail.com',
                "TransactionId" => $transactionId,
                "Custom1" => 'Custom1 anything',
                // Add other required fields... i.e. Custom2
            ];
            $responseSkipcashResponse = $this->generatePaymentLinkSkipcash($requestData);
            
            //Save results to db
            $skipcashLogs = new SkipcashLogs();
            $skipcashLogs->user_id = \Auth::user()->id??0;
            $skipcashLogs->logs = 'payUrl '.$responseSkipcashResponse;
            $skipcashLogs->save();

            $responseSkipcashArr = json_decode($responseSkipcashResponse, true);            
            if(isset($responseSkipcashArr['resultObj'])){
                $payUrl = $responseSkipcashArr['resultObj']['payUrl'];
                header('Content-Type: application/json; charset=utf-8');
                header("Location:".$payUrl);
                exit;
            }else{
                dd('Something went wrong...', $responseSkipcashArr);
            }

        }catch(\Throwable $e){
            throw $e;
        }
    }

    public function paymentGatewayResponseSkipcash(Request $request){
        try{

            $payment_id = $request->get('id');
            if(!isset($payment_id))
                dd('Something went wrong');

            $currentUrlWithParams = url()->full();
            $skipcashLogs = new SkipcashLogs();
            $skipcashLogs->user_id = \Auth::user()->id??0;
            $skipcashLogs->logs = 'returning '.$currentUrlWithParams;
            $skipcashLogs->save();
        
            $responseSkipcash = $this->validatePaymentSkipcash($payment_id);
            

            $skipcashLogs = new SkipcashLogs();
            $skipcashLogs->user_id = \Auth::user()->id??0;
            $skipcashLogs->logs = 'verifying '.$responseSkipcash;
            $skipcashLogs->save();

            $responseSkipcashArr = json_decode($responseSkipcash, true);
            $resultObj = $responseSkipcashArr['resultObj'];
            //Payment success
            if(isset($resultObj['statusId']) && $resultObj['statusId']===2){
                //Your custom transaciton id or order id from the payment gateway
                $transactionId = $resultObj['transactionId'];
                dd('transactionId '.$transactionId.' is verifid payment please update your order.');
            }else{
                dd("Payment failed...");
            }
        }catch(\Exception $e){
            throw $e;
        }
    }

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
