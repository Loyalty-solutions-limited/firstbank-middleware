<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\getBillerItemRequest;
use App\Http\Requests\getBillerCategoryRequest;
use App\Http\Requests\sendBillPaymentAdviceRequest;

class BAPController extends BaseController
{
    public function getBillerCategory()
    {
        $payload = "<SearchCriteria><TerminalId>3FCL0001</TerminalId></SearchCriteria>";
        return $this->postDataGuzzle($payload, "GetBillerCategories");
        // return $this->postData("", "GetBillerCategories");
    }
    

    public function getBillers(getBillerCategoryRequest $request)
    {
        //return $request->cat_id;
        $payload = "<SearchCriteria>
                       <TerminalId>3FCL0001</TerminalId>
                       <CategoryId>$request->cat_id</CategoryId>
                    </SearchCriteria>";
        // $payload = "<SearchCriteria><TerminalId>3FAP0001</TerminalId><CategoryId>$request->cat_id</CategoryId></SearchCriteria>";
        return $this->postDataGuzzle($payload, "GetBillers");
         //return $this->postData($payload, "GetBillers");
    }

    public function getBillerItems(getBillerItemRequest $request)
    {
        $payload = "<SearchCriteria>
                      <BillerId>$request->biller_id</BillerId>
                      <TerminalId>3FCL0001</TerminalId>
                    </SearchCriteria>";
        // $payload = "<SearchCriteria><BillerId>$request->biller_id</BillerId><TerminalId>3FAP0001</TerminalId></SearchCriteria>";

        //return $this->postData($payload, "GetBillerPaymentItems");
         return $this->postDataGuzzle($payload, "GetBillerPaymentItems");
    }

    public function sendBillPaymentAdvice(sendBillPaymentAdviceRequest $request)
    {
        $trans_ref = random_int(999999, 1000000000);
        $payload = "<BillPaymentAdvice>
                        <Amount>$request->amount</Amount>
                        <PaymentCode>$request->payment_code</PaymentCode>
                        <RequestReference>$trans_ref</RequestReference>
                        <SuspenseAccount />
                        <TerminalId>3FCL0001</TerminalId>
                        <CustomerAccountNumber>$request->customer_account_number</CustomerAccountNumber>
                        <CustomerId>$request->customer_id</CustomerId>
                        <CustomerMobile>$request->customer_mobile</CustomerMobile>
                        <CustomerEmail>a@b.com</CustomerEmail>
                        <Narration>Trans_ref$trans_ref/$request->customer_account_number/$request->customer_mobile</Narration>
                    </BillPaymentAdvice>";
            //$payload;
            
            dd($this->postDataGuzzle($payload, "SendBillPaymentAdvice"));
             //return $this->postData($payload, "SendBillPaymentAdvice");

    }
}