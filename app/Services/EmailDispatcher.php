<?php

namespace App\Services;

use App\Services\UserService;
use App\Services\CurlService;
use App\Models\Company;
use App\Models\EmailReportLog;
use App\Models\EmailTemplate;
use App\Models\Enrollment;
use App\Models\PendingEmails;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\PendingMail;


//require "../../vendor/sendgrid/autoload.php";

class EmailDispatcher{

    private $content = '';
    private $plainContent = '';
    private $userDetails = [];

    public function fetchUserDetails(Request $request){
        $userID =  new UserService($request->enrollment_id);
        $fname = $userID->first_name;
        $lname = $userID->last_name;
        $this->userDetails = array("first_name"=>$fname, "last_name"=>$lname);
        return $this->userDetails;
    }

    public function replaceString($array, $content){
        $step1 = str_ireplace(array('$firstname', '$lastname'), $array, $content);
        $step2 = $step1;
        $this->content = $step2;
        $this->plainContent = strip_tags($step2);
        $emailContents = array($this->content, $this->plainContent);
        return $emailContents;
    }

    public static function sendPendingEnrolmentEmails(){
        $array_of_response = array();
        $count = 0;
        $pendingMails = PendingEmails::where('status',0)->where('tries', '<=', 3)->where('subject', 'YOU JUST EARNED LOYALTY POINTS ON THE FIDELITY GREEN REWARDS PROGRAMME')->limit(700);//->get();
		//print_r($pendingMails->get());exit;
        if ($pendingMails->count() > 0 ){
            foreach ($pendingMails->get() as $pendingMail){

                $user = Enrollment::where('cif_id', $pendingMail->enrolment_id)->first();
                if (!empty($user)){
                $recipient = trim($user->email);
                $snd_mail = self::sendMail($pendingMail->subject, $pendingMail->body, $recipient);
               // if($snd_mail == 1) {
                    self::unPendMail($pendingMail->id);
                    $count++;
                    array_push($array_of_response, array("completed $count mails $snd_mail"));
               // }

                }

            }
            }else{

            array_push($array_of_response, array("no pending mails."));
        }
        return $array_of_response;



    }

    public static function unPendMail($pendingMailID){
        $tries = PendingEmails::find($pendingMailID);
        PendingEmails::where('id', $pendingMailID)->update(['status'=>1, 'tries'=>$tries->tries + 1]);
    }

    // public function Dispatch
      public static function sendMail($mail_subject, $mail_body, $recipient){

        $data = array(
         "subject"=>$mail_subject, "to"=>$recipient, "email" => $recipient, "body"=>$mail_body);
        $mail_sent_response =   self::testCurl(http_build_query($data));
        if (!$mail_sent_response){
            return 0;
        }
         return 1;

    }


    public static function sendInfoBip(){

        //


    }


    /* Email Migration Code*/

    public static function pendMails($customer_ref, $subject, $body, $from){
        if($subject != null){
            $new_record['suject'] = $subject;
        }
        if($from != null){
            $new_record['from'] = $from;
        }
        $new_record = array('enrolment_id'=>$customer_ref, 'body'=>$body, 'template_id'=>0, 'subject'=>$subject, 'from'=>$from);
        PendingEmails::create($new_record);
    }


    public static function buildEnrolmentTemplate(array $placeholders, array $values){
        $str = '<!DOCTYPE HTML>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

            <style>
                :root{
                    --fidelity-green:#4FC143;
                    --fidelity-blue:#0E237D;
                }

                *{
                    margin:0;
                    padding:0;
                    box-sizing: border-box;

                }

                body{

                    font-family: "Open Sans", sans-serif, serif;
                }

                .overall-template-container{
                    max-width: 620px !important;
                    margin: 0px auto !important;
                    padding: 20px !important;
                }

                #voucher-del-header{
                    position: relative !important;
                    height: auto;
                    padding: 0;
                    margin: 0;
                    /* display: flex !important;
                    justify-content: flex-end !important;
                    align-items: center !important; */
                }

                #header-corner-green{
                    position: absolute !important;
                    top:0 !important;
                    left:0 !important;
                    background-color: #4FC143 !important;
                    width: 42px !important;
                    height: 42px !important;
                    border-radius: 0px 0px 50px 0px !important;
                }

                #email-header-imgCon{
                    /* height: 40px !important;
                    width: 170px !important;
                    margin-right: 20px !important; */
                    height: 100%;
                    width:100%;
                    margin: auto;
                }

                .overall-template-container>img:first-child{
                    /* height: 100%; */
                    max-width: 600px;
                    padding: 0;
                    display: block;
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
                    padding: 25px 0;
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
                     margin-bottom: 75px;

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
                    padding: 20px 10px;
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
                    background-color: #0E237D;
                    padding: 20px ;
                    margin-top: 50px;
                }

               #disclaimer-body{
                   padding-bottom: 30px;
                   color: #fff;
                   font-size: 14px;
               }

               #template-footer-bottom{
                   display: flex;
                   justify-content: space-between;
                   align-items: center;
                   padding: 20px 0;
               }

               #footer-socials-area img{
                    height: 18px;
                    margin-right: 15px;
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
                   /* background-color: aqua; */
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
                   background-color: #4FC143;
                   color: #fff;
                   height: 48px;
                   width: 100%;
                   margin-top: 10px;
                   border: none;
                   border-radius: 5px;
               }





                @media(max-width:1440px){

                }

                @media(max-width:1024px){

                    #email-header-imgCon>img{
                        object-fit: contain;
                    }

                }

                @media(max-width:900px){
                 .overall-template-container{
                    	max-width:850px !important;
                    }
                }

                @media (max-width: 500px){

        			.overall-template-container{
                    	max-width:450px !important;
                    }
                    #email-header-imgCon{
                        height: 100%;
                    }

                    #template-footer-bottom{
                        flex-direction: column;
                        justify-content: space-between;
                        align-items: center;
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

                    /* #vemail-body-imgCon{
                        height: 200px !important;
                    } */

                }

                @media (max-width: 320px){

                    /* #vemail-body-imgCon{
                        height: 180px !important;
                    } */

                }
            </style>

        </head>
        <body>
            <div class="overall-template-container">

                        <img id="template-header-img"src="https://loyaltysolutionsnigeria.com/email_templates/images/template-header.png" alt="" style="height:60px; max-width:100%"/>

                        <img id="template-banner-img"src="https://loyaltysolutionsnigeria.com/email_templates/images/customer-enrollment-banner.png" style="max-width:100%" alt>

                <section id="message-content">

                    <div id="template-name-area">
                        <p>Dear <strong>$first_name $Last_name</strong> (<b>$membership_id</b>),</p>
                <p>Congratulations!</p>
                    <p>We value your loyalty to us at Fidelity Bank Plc and as a way of showing our gratitude, you have been selected as one of our esteemed customers to enjoy amazing rewards on the Fidelity Green Rewards Loyalty Programme.</p>
               <p>You earn points on the Fidelity Green Rewards Loyalty Programme when you carry out transactions (bill payments, airtime purchase, funds transfer, etc.) on any of our alternative banking channels such as the Fidelity Mobile App, Fidelity ATMs, Fidelity POSs, Fidelity *770# (Instant Banking), etc.</p>
        <p>Using your earned points, you can redeem items such as airtime, movie tickets, shopping vouchers, electronic gadgets, airline tickets and so much more, on the Fidelity Green Rewards Mart.</p>
        <p>Simply log on to the rewards portal with the details below:</p>

            <p><strong>Membership No - $membership_id <br>  Password - $password</strong></p>

            <p>Click <a href="https://loyalty.fidelitybank.ng/login.php" target="blank">here</a> to get started</p>

            <p>Alternatively, you can access your loyalty account via the Fidelity Online Banking Application.</p>

            <p>Do not hold back, the experience will blow your mind!</p>
             </div>

                    <div style="">
                        <p> For enquiries, please call our interactive Contact Centre to speak with any of our agents on 070034335489 or 09087989069. You can also send an an email to
                            <a href="mailto:true.serve@fidelitybank.ng ">true.serve@fidelitybank.ng </a> </p>
                            <p>If you are calling from outside Nigeria, please dial +2349087989069.</p>
                            <br>
                        <p>Thank you for choosing Fidelity Bank Plc.</p>

                    </div>

                </section>

                <footer id="redeem-template-footer" style="background-color:#0E237D !important">
                    <div id="disclaimer-body">
                        <p>Please note that Fidelity Bank would NEVER request for your account information or an update of your banking details (including BVN and REWARD POINTS) via email or telephone. Please DISREGARD and DELETE all such emails and SMSs as they are messages intended to defraud you. In addition, NEVER generate a token or passcode for anyone via telephone, email, or internet chat.  </p>
                    </div>

                    <hr style="border: 1px solid #E2E8F0;">

                    <div id="template-footer-bottom">
                        <div id="footer-socials-area">
                            <a href="https://facebook.com/FidelityBankplc"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/facebook.png" alt="facebook-logo" srcset=""></a>
                            <a href="https://www.instagram.com/fidelitybankplc/"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/Instagram.png" alt="instagram-logo" srcset=""></a>
                            <a href="https://www.linkedin.com/company/fidelitybankplc"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/LinkedIn.png" alt="linkedin-logo" srcset=""></a>
                            <a href="https://twitter.com/fidelitybankplc"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/Twitter.png" alt="twitter-logo" srcset=""></a>
                            <!--<a href="#"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/Google.png" alt="google-logo" srcset=""></a>-->

                        </div>


                        <div id="template-web-links">
                            <a href="https://www.fidelitybank.com" style="color:white !important; text-decoration: none">www.fidelitybank.com</a>
                        </div>
                    </div>


                </footer>
            </div>

        </body>
        </html>';
        return self::replaceVariables($placeholders, $values, $str);
    }


     public static function buildTransactionTemplate(array $placeholders, array $values){

        $str = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

        <style>
        *{
            / font-family: "Open Sans", sans-serif, serif; /
            font-family: "Open Sans";
        }

        body{
             max-width: 600px;
            / text-align: center; /
            / padding: au /
        }
        .wrapper{
            max-width: 100% !important; margin: 0px auto;
        }
        div.preheader{

            padding: 0px;
            height: 15px;
        }
        .green-curve{
            width: 12px;
            height: 15px;
            background: #4FC143;
            border-radius: 0px 0px 50px 0px;
        }
        .tagline{
            position: absolute;
            right: 30px;
             top: 20px;
            color: gray;
            font-size: 15px;
        }
        .logo{
            text-align: end;
        }
        .logo-img{
            width:142x;
             height:35px;
        }
        div{
            padding: 15px;
        }

        #header{
            text-align: center;
        }

        .img-banner{
            /* width: 100%;

            height: 100%; */
            max-width: 600px;
        }


        section, main, header, footer{
            / max-width: 600px; /
        }
        section.message-content{
            font-family: "Open Sans";
            font-style: normal;
            font-weight: 400;
            font-size: 16px;
            line-height: 140.62%;

            / or 22px /

            / Neutral/900 /
            color: #0F172A;
            padding: 12px 22px;
        }
        footer.footer{
            background: #0E237D;
            color: white;
            padding: 20px 22px 12px 22px;
        }
        .disclaimer-message{
            font-family: "Open Sans";
            font-style: normal;
            font-weight: 400;
            font-size: 12px;
            line-height: 140.62%;

            / or 17px /
        }
        .rowss{
            / display: inline; /
            display: flex;
            flex-direction: row;
        }
        div.socials{
            width: fit-content;
          / display: flex; /
           / flex-direction: row; /

        }
        img{
            max-width: 100% !important;
        }
        div.socials img{
            width: 15px;
            height:15px;
        }
        div.web-link{
            /* width: 202px;
            height: 28px; */
            font-family: "Fontin";
            font-style: normal;
            font-weight: 400;
            font-size: 20px;
            line-height: 140.62%;

            / or 28px /
            text-align: right;

            color: #FFFFFF;
        }
        div.space{
            flex-grow: 8;
        }
        @media screen and (max-width: 600px) {
            .logo-img{
            width:120x;
             height:25px;
            }
        }

        .items{
                    max-width: 600px !important;
                }
                .item-list{
                    display: flex;
                    flex-direction: row;
                }
                .product-items{
                    / margin:10px; /
                    width: 266px;
                }
                .item-img-block{
                    / background: lightgray; /
                    /* width:266px;
                    height: 252px;
                    border-radius: 5px; */
                }
                .item-img{
                    width:266px;
                    height: 252px;
                }
                .item-description{

                    font-family: "Open Sans";
                    font-style: normal;
                    font-weight: 400;
                    font-size: 20px;
                    line-height: 140.62%;

                    / or 28px /

                    / almost black /
                    color: #1C1E23;

                    / Inside auto layout /
                    flex: none;
                    order: 1;
                    flex-grow: 0;
                }
                .item-point{

                    font-family: "Outfit" !important;
                    font-style: normal;
                    font-weight: 600;
                    font-size: 20px;
                    line-height: 140.62%;

                    / or 28px /

                    color: #000000;

                    / Inside auto layout /
                    flex: none;
                    order: 2;
                    flex-grow: 0;
                }
                .item-redeem-btn{
                    justify-content: center;
                    align-items: center;
                    padding: 12px 30px;
                    text-decoration: none;
                    / F-Green /
                    background: #4FC143;
                    border-radius: 5px;
                    color: white;

                    / Inside auto layout /
                    flex: none;
                    order: 0;
                    flex-grow: 0;
                }

                @media (max-width: 500px) {
                    .item-list{
                        flex-direction: column;
                    }
                }
    </style>


        </head>
        <body>
        <div class="wrapper" style="">
            <table>
            <tr>

            <header>

                <div class="preheader">
                    <div class="green-curve"></div>
                </div>

                <div class="logo">
                    <img class="logo-img" src="https://loyaltysolutionsnigeria.com/email_templates/images/Logo2.png" alt="fidelity Logo" >
                </div>
            </header>

            </table>

            <main>

                <!-- img banner -->
                <section class="img-banner">
                         <img src="https://loyaltysolutionsnigeria.com/email_templates/images/points-accumulation.png" class="banner" alt="" >
                </section>

        <section class="message-content">
            <p><strong>Dear $first_name,</strong></p>
            <p>Thank you for banking with us.</p>
            <p>You just earned <strong>$points_earned</strong> Points from transacting with your  <b>$product_name</b> and your current loyalty points balance is <strong>$current_balance</strong> points. </p>

            <p>Using your earned points, you can redeem items such as airtime, movie tickets, shopping vouchers, electronic gadgets, airline tickets and so much more, on the Fidelity Green Rewards Mart.</p>

            <p>To accumulate more points on the Fidelity Green Rewards Loyalty Programme, simply carry out your transactions (bill payments, airtime purchase, funds transfer, etc.) on any of our alternative banking channels such as the Fidelity Mobile App, Fidelity ATMs, Fidelity POSs, Fidelity *770# (Instant Banking), etc.</p>

            <p>You can access your Loyalty Account here  <a href="$link">here</a> by logging in with your Membership ID and Password. To reset your password, kindly follow the password reset link on the portal and your details will be sent to your registered email address. </p>

           <!-- <p>Membership ID: <strong>$Membership_ID</strong><br>
Password: If you have forgotten your password, click on Reset password on the log-in page and this will be sent to you. </p><p>
Alternatively, you can access your loyalty account via your Mobile and Online Banking Applications.
</p> -->
            <p>
              Alternatively, you can access your loyalty account via the Fidelity Online Banking Application.
            </p>

            <section class="items">

                <p>For enquiries, please call our interactive Contact Centre to speak to any of our agents on 070034335489 or 09087989069. You can also send an email to  <a href="mailto:true.serve@fidelitybank.ng">true.serve@fidelitybank.ng</a>
                .</p>
                <p>If you are calling from outside Nigeria, please dial +2349087989069.</p>

            <p>
        Thank you for choosing Fidelity Bank Plc.</p>
        </section>


        </main>
        <footer class="footer">
                <!-- <div class="logo">
                    <a href="#"><img src="#" alt="fidelity Logo"></a>
                </div> -->
                <div class="disclaimer-body">
                    <p class="disclaimer-msg" style="font-size: small;"><strong>Please note that Fidelity Bank would NEVER request for your account information or an update of your banking details (including BVN and REWARD POINT) via email or telephone. Please DISREGARD and DELETE such emails and SMS as they are  messages intended to defraud you. In addition, NEVER generate a token or passcode for anyone via telephone, email or internet chat.</strong></p>
                </div>
                <div>
                <hr style="opacity: 0.2; border: 1px solid #E2E8F0;">

                </div>
                <div class="rowss">
                    <div class="socials">
                        <a href="https://facebook.com/FidelityBankplc"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/facebook.png" alt="facebook-logo" srcset=""></a>
                        <a href="https://www.instagram.com/fidelitybankplc/"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/Instagram.png" alt="instagram-logo" srcset=""></a>
                        <a href="https://www.linkedin.com/company/fidelitybankplc"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/LinkedIn.png" alt="linkedin-logo" srcset=""></a>
                        <a href="https://twitter.com/fidelitybankplc"><img src="https://loyaltysolutionsnigeria.com/email_templates/images/Twitter.png" alt="twitter-logo" srcset=""></a>
                    </div>
                    <div class="space" ></div>
                    <div class="web-link">
                        <a href="www.fidelitybank.com" style="color:white !important; text-decoration: none">www.fidelitybank.com</a>
                    </div>
                </div>


        </footer>


        </body>
        </html>';
        return self::replaceVariables($placeholders, $values, $str);
    }

    public static function BuildReportTemplate($placeholders, $values){
        $str = '<!DOCTYPE html>
        <html>
        <body>

        <table>
        <tr>
        <td>Hello Fidelity,</td>
        </tr>
        </table>
        <br>

        <table>
        <tr>
        <td>A total of $count transactions were uploaded to the middleware on $created_at for transactions done between $date_from & $date_to, kindly see the status report of the transactions.</td>

        </tr>
        </table>
        <div style="margin-top:30px">
        <table>
        <tr>
        <td>Successful migrations: $successful</td>
        </tr>
        <tr>
        <td>Pending migrations: $pending</td>
        </tr>
        <tr>
        <td>Failed migrations: $failed</td>

        </tr>
        </div>
        </table>
        <br>
        <table>
        <td>The list of all failed transactions can be found in the attachment. <br>


        </td>

        </table><br>
        Regards.
        </body>
        </html>
        ';
        return self::replaceVariables($placeholders, $values, $str);
    }

    public static function replaceVariables($placeholders, $values, $str){
        $new_str = str_ireplace($placeholders, $values, $str);
        return $new_str;
    }

    public static function testCurl($data){

        $curl = curl_init();
        // $key = "Ocp-Apim-Subscription-Key"; $value = "a04d83e1f9844621842db0ad7bf9c480";
        // $headers = array(
        //     "Content-Type: application/json",
        //     "$key: $value",
        //     "type: text"
        //  );

        curl_setopt_array($curl, array(
        CURLOPT_URL =>  'https://loyaltysolutionsnigeria.com/email_templates/sendmail2.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_SSL_VERIFYPEER=>FALSE,
        //  CURLOPT_HTTPHEADER=>$headers,
        CURLOPT_POSTFIELDS => $data
        ));

        $response = curl_exec($curl);
        print_r($response);
        curl_close($curl);
        if (curl_errno($curl)) {
            return 0;
        }
        elseif(!$response){
            return 0;
        }else{
         return 1;
        }
    }

}
?>
