<?php

namespace Shahzadthathal\Skipcash\Traits;

trait SkipCashPaymentGatewayTrait {

    /**
     * Documentation
     * @link https://skipcash.app/assets/doc/SkipCashIntegrationManual.pdf
     * Visa Test Card Detail: John Smith, 4001919257537193 ,  12/2027, 123
    */


    /**
     * Generate Payment Link
     * @return Array with payment link
     */
    public function generatePaymentLinkSkipcash($postData){
        try{
            $postData['Amount'] = strval($postData['Amount']);
            $data_string = json_encode($postData);
            $resultheader = '';
            foreach ($postData as $key => $value) {
                $resultheader .= $key . '=' . $value . ',';
            }
            $resultheader = rtrim($resultheader, ',');
            $s = hash_hmac('sha256', $resultheader, config('skipcash.key_secret'), true);
            $authorisationheader = base64_encode($s);
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL =>  config('skipcash.url').'/api/v1/payments',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data_string,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/json', 'Authorization:' . $authorisationheader
                ),
            ));
            $response = curl_exec($curl);
            if ($response === false) {
                $error_message = curl_error($curl);
                $error_code = curl_errno($curl);
                die("cURL error (code $error_code): $error_message");
            }
            curl_close($curl);
            return  $response;
        }catch(\Throwable $e){
            throw $e;
        }
    }

    /**
     * Validate payment
     * @return Array
     */
    public function validatePaymentSkipcash($payment_id){
        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => config('skipcash.url').'/api/v1/payments/'.$payment_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Content-Type:application/json', 'Accept: application/json', 'Authorization: ' . config('skipcash.client_id')
                ),
            ));
            $response = curl_exec($curl);
            if ($response === false) {
                $error_message = curl_error($curl);
                $error_code = curl_errno($curl);
                die("cURL error (code $error_code): $error_message");
            }
            curl_close($curl);
            return  $response;
        }catch(\Throwable $e){
            throw $e;
        }
    }

    //Generate uuid
    function guidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}