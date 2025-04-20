<?php
namespace App\Services;

class CurlService{
    
    public static function doCURL($url, $postFields){

        $key = "Ocp-Apim-Subscription-Key"; $value = "a04d83e1f9844621842db0ad7bf9c480";
        $headers = array(
            "Content-Type: application/json",
            "$key: $value",
            "type: text"
         );
            $curl= curl_init();
            $ch = $curl;
            $timeout = 0; // Set 0 for no timeout.
            curl_setopt($ch, CURLOPT_URL, $url);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch). "";
            }else{
                $error_msg = '';
            }
            
                    curl_close($ch);
                    
                    return $result;
        }

    public static function makeGet($url){
        
        
        $timeout = 0; // Set 0 for no timeout.
        $cURLConnection = curl_init();
        $ch = $cURLConnection;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $phoneList = curl_exec($ch);
        curl_close($ch);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch). "";
        }else{
            $error_msg = '';
        }
        
                curl_close($ch);
             
                return $result;
    }

}


?>