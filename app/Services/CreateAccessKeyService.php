<?php 

namespace App\Services;
use App\Models\VendorAccessKey;

class CreateAccessKeyService {
    private static $key = 'LSL-'; 
    private static $url = "https://greenrewards.perxclm.com/logkey.php";
    private static $data = array();


    public static function index(){
        //create key, log key & send key
        self::createAccessKey();
        self::logKey(self::$key);
        self::sendKey(self::$url, self::$data);
    } 

    private static function createAccessKey(){

        $str = 'abcd#$ef78ghijklm65nopqrstuvwxyz';
        $str .= strtoupper($str);
        $str .= '123490!@#$#%^&*()_+';
        $str = str_split($str);
        
        $count = count($str);
        $key = ''; 
        $i = 0;

        while ($i < 16){
            $tm = strval(time()) . strrev(strval(time()));
            $tm = substr($tm, 4, strlen($tm));
            $random = $tm[$i] + 1;
            
            $key .= $str[$random - 1];
            $i++;
        }
       
        self::$key .= $key;
        self::$data["key"]=self::$key;
        return self::$data;

    }

    private static function logKey(string $key){
      VendorAccessKey::create(['value' => $key]);
    }

    private static function sendKey(string $url, array $data){
        

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt(
            $curl, 
            CURLOPT_HTTPHEADER, 
            array(
               // 'Content-Type: application/json', // for define content type that is json
              
            )
        );
        

        $result = curl_exec($curl);
        curl_close($curl);
     
    }

}