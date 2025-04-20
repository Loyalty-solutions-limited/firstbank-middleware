<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LoyaltyProgram;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class LoyaltyProgramController extends Controller
{
    public function AddProgram(Request $request){
        $validate = Validator::make($request->all(),[
            "company_id" => "required|numeric",
            "program_name" => "required|string",
            "currency" => "required|string",
            "image" => "string|nullable"
        ]);

        if($validate->fails()){
            return $this->sendBadRequestResponse('Error', $validate->errors());
        }

        try 
        { 
            $result = $this->add_program($request->company_id,$request->program_name,$request->currency);
            if(!$result){
                return $this->sendBadRequestResponse('Error', 'Program could not be added');
            }

            return $this->sendSuccessResponse('Success', $result);
        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }
    private function add_program($company_id, $name, $currency, $image = null)
    {
        
            $insert = new LoyaltyProgram();
            $insert->company_id = $company_id;
            $insert->name = $name;
            $insert->currency_name = $currency;
            $insert->image_url = $image;
            $insert->slug = Str::slug($name);
            $insert->status = 1;
            $insert->save();

            return $insert;
    }

    public function EditProgram(Request $request){
        try{
            $rem = $this->edit_program($request->id);
            
            if(!$rem){
                return $this->sendBadRequestResponse('Error');
            }

            return $this->sendSuccessResponse('Success', $rem);
    
        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
    }


    private function edit_program($id){

        $data = LoyaltyProgram::where('id',$id)->first();
        return $data;
    }

    public function UpdateProgram(Request $request){

        $validator = Validator::make($request->all(),[
            "program_name" => "required|string",
            "currency" => "required|string",
            "id" => "required|numeric"
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse('Error', $validator->errors());
        }

        try{

            $res = $this->update_program($request->id, $request->program_name, $request->currency);
            if(!$res){
                return $this->sendBadRequestResponse('Error', 'Loyalty program could not be updated');
            }

            return $this->sendSuccessResponse('Updated Successfully', $res);
        }

        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    public function FetchPrograms(){
        return LoyaltyProgram::where('status',1)->get();
    }

    private function update_program($id, $name, $currency)
    {
        
            $edit = LoyaltyProgram::where('id', $id)->update(['name' => $name, 'currency_name'=>$currency]);

            return true;
        
    }

    private function edit_program_image($id, $url)
    {
        try 
        { 

            $edit = LoyaltyProgram::where('id', $id)->update(['image_url' => $url]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    private function update_status($id, $status)
    {
        try 
        { 

            $edit = LoyaltyProgram::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    public function GetPrograms(Request $request){

        $validate = Validator::make($request->all(),[
            "company_id" => "required|numeric",
            "status" => "nullable|numeric"
        ]);
    
        if($validate->fails()){
            return $this->sendBadRequestResponse('Error', $validate->errors());
        }

        try{

            $out = $this->view_loyalty_programs($request->status,$request->company_id);
            if(!$out){

                return $this->sendBadRequestResponse('Error', 'Loyalty programs could not be retrieved');
            }

            return $out;
        }

        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    private function view_loyalty_programs($status = null, $company_id)
    {

            $view = DB::table('loyalty_programs')
                    ->select('id','company_id','name','slug','currency_name', 'image_url', 'status');
            if(isset($status))
            {
                $view->where('status', $status);
            }
            if(isset($company_id))
            {
                $view->where('company_id', $company_id);
            }

            $data = [];
            $data['status'] = 'Success';
            $data['status_code'] = 1;
            $data['data'] = $view->paginate(10);

            return $data;
    }

}
