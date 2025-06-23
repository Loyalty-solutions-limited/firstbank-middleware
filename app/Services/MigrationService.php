<?php

namespace App\Services;
use GuzzleHttp\Client;
use App\Models\Enrollment;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class MigrationService {
    public static $key = '!QAZXSW@#EDCVFR$';
    public static $iv = '5666685225155700';
    protected static $program = "Firstbank Green Rewards Programme";
    protected static $placeholders = array('$memberID', '$first_name', '$last_name', '$pin', '$points', '$email', '$program');
    protected static $link = "https://firstreward.firstbanknigeria.com/login.php";


    protected static $headerPayload = array(
        //'Content-Type: application/json',
    );
    protected static $url = "https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/api/v1/index.php";
    // protected static $url = "https://fbn.perxclm5.com/api/v1/index.php";

    public function __construct()
    {

    }
    protected static function pushToPERX($url="https://fbnperxlive-amfgcwc2d9g0e9av.francecentral-01.azurewebsites.net/api/v1/index.php", $postFields, $payload)
    {

        $curl= curl_init();
        $ch = $curl;
        $timeout = 0; // Set 0 for no timeout.
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type= application/json"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $result = curl_exec($ch);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch). "^^";
}else{
	$error_msg = '';
}

        curl_close($ch);

        return $result;
    }
    public static function string_encrypt($string, $key, $iv) : string{
        $ciphering = "AES-128-CTR";
        $encryption_iv = '1234567891011121';
        $key = "SmoothJay";
        $options = 0;
        $encryption = openssl_encrypt($string, $ciphering,
            $key, $options, $iv);
        return $encryption;
    }

    public static function string_decrypt($encryption, $key, $iv) : string{
         $ciphering = "AES-128-CTR";
        $decryption_iv = '1234567891011121'; //1234567891011121
        $key = "SmoothJay";
        $options = 0;
        $decryption=openssl_decrypt($encryption, $ciphering,
        $key, $options, $iv);
        return $decryption;
    }

public static function passwordReturn(){
    return self::string_encrypt('ssw0rd20', self::$key,self::$iv);
}
   // self::$password = parent::string_encrypt('Di@mond10$#', self::$key,self::$iv);



public static function resolveMemberReference($cif_id){
    $cif_id = Enrollment::where('cif_id', $cif_id)->select('cif_id')->first();
    return $cif_id ? $cif_id->cif_id:null;
}

public static function isJSON($string){
	return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

public static function sendMailGuzzle($request)
{
        $url = env('POINTS_TO_CASH_URL') . "email/send-email";


        // $res = Http::withOptions(['verify' => false])->post($url);

        $client = new Client(['verify' => false]);
        $headers = [
        'Accept' => 'application/json',
        'AppId' => config("externalservices.POINTS_TO_CASH_APPID"),
        'AppKey' => config("externalservices.POINTS_TO_CASH_APPKEY"),
        'Content-Type' => 'application/json',
        'Cookie' => 'ARRAffinity=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33; ARRAffinitySameSite=8cb9eb8a9c8e49bb32964ef5e087477636164e3b1bd119e62b62b2d516d04b33'
        ];

        // print_r(json_encode($request->all()));

        $request = new GuzzleRequest('POST', $url, $headers, json_encode($request));
        $res = $client->sendAsync($request)->wait();
        echo $res->getBody();


}


public static function buildEnrolmentTemplate(array $placeholders, array $values)
{
    $str = '<!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <style>

                *{
                    margin:0;
                    padding:0;
                    box-sizing: border-box;

                }

                body{

                    font-family: "Inter", sans-serif, serif;
                }

                .overall-template-container{
                    max-width: 700px !important;
                    margin: 0px auto !important;
                    padding: 20px !important;
                }

                #voucher-del-header{
                    position: relative !important;
                    height: auto;
                    padding: 0;
                    margin: 0;

                }


                #email-header-imgCon{

                    height: 100%;
                    width:100%;
                    margin: auto;
                }

                .overall-template-container>img:first-child{

                    display: -webkit-box;
                    margin-left: auto;
                }

                #vemail-body-imgCon{
                    height: fit-content;
                    /* height: 500px; */
                }


                .overall-template-container>#template-banner-img{
                    display: block;
                    /* height: 50%; */
                    width: 100%;
                    padding: 0;
                    /* object-fit: contain; */
                }

                #message-content{
                    padding: 0px 20px;
                }


                #template-name-area{
                    padding: 15px 0;
                }

                #template-date-area{
                    padding-bottom: 50px;
                }

                #date-and-point-box{
                    display: flex;
                    justify-content: space-between;
                    gap:20px;
                }

                #template-name-area>p{
                    margin: 15px 0;
                }

                /* TABLE AREA */

                .table-wrapper{
                    margin-bottom: 50px;
                    overflow:auto;

                }

                #voucher-redeem-table{
                    width:100%;
                    border-bottom: 2px solid black;
                    border-collapse: collapse;
                    margin-left: 0px;
                    margin-right: 0px;
                    padding: 0px;
                    margin-bottom: 15px;

                }

                #first-row{
                    border-bottom: 2px solid !important;
                }

                #voucher-redeem-table thead{
                    border-bottom:1px solid black
                }

                #voucher-redeem-table tr:nth-child(even) {
                    background-color: #F1F5F9;
                }


            #voucher-redeem-table td,
                #voucher-redeem-table th{
                    padding: 10px 10px;
                }

                #voucher-redeem-table th{
                    color: #64748B;
                }


                /* PICKUP AREA */
            #pickup-box{
                display: flex;
                gap:30px;
                margin-bottom: 20px;
            }

            #pickup-box>div{
                width: 50%;;
            }

            #pickup-words>b{
                display: block;
                margin-bottom: 10px;
            }

            #pickup-image-container>img{
                height: 100%;
                width: 100%;
                object-fit: cover;
            }

                /* FOOTER AREA */
                #redeem-template-footer{
                    background-color: #002955;
                    padding: 20px ;
                    margin-top: 30px;
                    font-family: Inter;
                    font-size: 12px;
                    font-weight: 500;
                    line-height: 16.88px;
                    /* letter-spacing: 0em; */
                    text-align:left;
                    width: 100%;

                }

                #footer_img{
                    height: 107px;
                    width: 113px;
                    left: 19px;
                    top: 22px;
                    border-radius: 0px;
                    float:left;
                }

                #footer_banner{
                    height: 156.8564453125px;
                    width: 100% !important;

                }

            #disclaimer-body{
                padding-bottom: 30px;
                color: #fff;
                font-size: 12px;
            }

            #template-footer-bottom{
                background-color: #002955;
                /* display: flex; */
                justify-content: space-between;
                /* align-items: center !important; */
                text-align: center !important;
                padding: 20px 0;
                color: #fff;
                font-size: 12px;
                font-weight: 500;
            }

            #footer-socials-area img{
                    height: 18px;
                    margin-right: 15px;
                    /* background-color: #447BBE; */
            }

            #footer-socials-area{
                margin-bottom: 15px;
            }

            /* ITEMS YOU MIGHT LIKE AREA */

            #items-like-container{
                margin-top: 50px;
            }

            #items-like-container>h3{
                text-align: center;
            }

            #items-like-filter{
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: space-evenly;
            }

            #single-might-item{
                width: 160px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            #single-might-item>p{
                text-align: center;
                margin: 10px 0;
            }

            #single-might-item>b{
                align-self: center;
            }

            #single-might-item>a{
                text-decoration:none;
                width:100%;
            }

            #single-might-item>a>button{
                display:block;
                color: #fff;
                height: 48px;
                width: 100%;
                margin-top: 10px;
                border: none;
                border-radius: 5px;
            }

            .left-align{
                text-align: left;
            }

                .right-align{
                text-align: right;
            }


                @media(max-width:1440px){

                }

                @media(max-width:1024px){

                    #email-header-imgCon>img{
                        object-fit: contain;
                    }

                }

                @media(max-width:900px){

                }

                @media (max-width: 500px){


                    #email-header-imgCon{
                        height: 100%;
                    }

                    #pickup-box{
                        flex-direction: column;
                    }

                    #pickup-box>div{
                        width: 100%;
                    }

                    #pickup-details>div:last-child{
                        flex-direction: column-reverse;
                    }

                    #items-like-filter{
                        justify-content: center;
                    }
                }

                @media (max-width: 400px){

                }

                @media (max-width: 320px){


                }
            </style>

        </head>
        <body>
            <div class="overall-template-container">

                        <img id="template-header-img" src="https://loyaltysolutionsnigeria.com/fbn_templates/images/fb_logo.png" alt=""/><br>

                        <img id="template-banner-img" src="https://loyaltysolutionsnigeria.com/fbn_templates/images/enrollment.png" alt>

                <section id="message-content">

                    <div id="template-name-area">
                        <p>Dear FirstName LastName ($membershipID)</p>
                    <p>We are excited to announce the launch of our new Customer Loyalty Program $LoyaltyProgramName brought to you by FirstBank. We have lots of exciting and exclusive rewards to say ‘Thank You’ for your continuous patronage. </p>
            <p>The $LoyaltyProgramName allows you to earn loyalty points called $CurrencyName, enjoy discounts from various merchants across the country, and many more benefits.</p>
        <p>Please note that your item(s) would be shipped to the indicated address within 15 working days for delivery.</p>
        <p>This is our way of saying thank you for your loyalty. Every time you use your card, e-channel, or any of our platforms, you will earn points that can be redeemed as shopping vouchers, movie tickets, and much more. </p>
        <p>You can access your loyalty account when you log in to your FirstMobile App or FirstOnline.. You can also use the dedicated portal $here to access your loyalty account, to log in, kindly use the details below.</p>
        <p>Username - $MembershipID </p>
        <p>Password - $Password (Kindly change this as soon as you login) </p>
        <p>Pin - $Pin (Only used when transferring points to your loyalty beneficiaries) </p>
        <p>Journey with us, it’s going to be an amazing ride.</p>
        <p>Have a question? We are here to help. Contact us today on 0708 062 5000, or send an email to <a href="mailto:firstcontact@firstbanknigeria.com" style="text-decoration:none;">firstcontact@firstbanknigeria.com</a>. You can also access the FAQs on the Loyalty Portal site. </p>
        <p>Thank you for trusting us enough to put You First</p>

            </div>

                </section>

                <footer id="redeem-template-footer">
                    <div id="disclaimer-body">
                        <img src="https://loyaltysolutionsnigeria.com/fbn_templates/images/footer_key.png" id="footer_img" style="margin-right: 20px;">
                        <p>Please note that FirstBank would never request for your account details or credentials such as membership number, BVN, PIN or password via email, telephone, or otherwise.
                            Should you receive any request for such information, please disregard it and report to the bank.
                        </p>
                    </div><br><br>
                </footer>
                <div style="background-color: #002955;">
                    <img src="https://loyaltysolutionsnigeria.com/fbn_templates/images/footer_banner.png" id="footer_banner">
                    <br>
                </div>

                <div id="template-footer-bottom">
                    <p>Please follow us on our social media handles</p><br><br>
                    <div id="footer-socials-area">
                        <a href="https://www.facebook.com/firstbankofnigeria"><img src="https://loyaltysolutionsnigeria.com/fbn_templates/images/fb.png" alt="facebook-logo" srcset=""></a>
                        <a href="https://instagram.com/firstbanknigeria/"><img src="https://loyaltysolutionsnigeria.com/fbn_templates/images/instagram.png" alt="instagram-logo" srcset=""></a>
                        <a href="https://www.linkedin.com/company/first-bank-of-nigeria-ltd/"><img src="https://loyaltysolutionsnigeria.com/fbn_templates/images/linkedln.png" alt="linkedin-logo" srcset=""></a>
                        <a href="https://twitter.com/firstbankngr"><img src="https://loyaltysolutionsnigeria.com/fbn_templates/images/twitter.png" alt="twitter-logo" srcset=""></a>

                    </div><br>
                    <p style="display:flex; margin-right: 100px; margin-left: 100px;">
                        For enquiries on FirstBank products and services, please call on:
                        firstcontact@firstbanknigeria.com +234 708 062 5000
                        Samuel Asabia House 35 Marina P.O. Box 5216, Lagos, Nigeria.
                    </p>
                    <br><br>
                </div>
            </div>
        </body>
        </html>';

    return self::replaceVariables($placeholders, $values, $str);
}

public static function replaceVariables($placeholders, $values, $str)
{
    $new_str = str_ireplace($placeholders, $values, $str);
    return $new_str;
}

}

?>