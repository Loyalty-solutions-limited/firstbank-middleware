<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Requests\AddCompanyRequest;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    //
        public function addCompany(Request $request){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);

            if($validator->fails())
            {
                return $this->sendBadRequestResponse($validator->errors());
            }

            $company = $this->add_company($request->name);
            if($company){
                return $this->sendSuccessResponse('Company added successfully');
                //return $request->route()->getName();
            }

        }


    private function add_company($name)
    {
        try
        {
            $insert = new Company();
            $insert->company_name = $name;
            $insert->status = 1;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }

    public function viewCompany(Request $request){

        try
        {
            //$edit = Company::where('id', $id)->update(['company_name' => $company_name]);
            $edit = $this->view_company($request->id);

           if($edit){

               return $edit;

            }

            return $this->sendBadRequestResponse('Company not found');
        }


        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
    }


    private function view_company($id)
    {
            return Company::where('id', $id)->first();

    }

    public function updateStatus(Request $request, $id, $status){
        try {

            $status_state = "";
            if($status == "enable" || $status == "active")
            {
                $status_state = 1;
            }
            else if ($status =="disable" ||$status == "inactive")
            {
                $status_state = 0;
            }
            else
            {
                return $this->sendBadRequestResponse([], "Wrong Status Defination");
            }
            $updateStatus = $this->update_status($id, $status_state);

            if($updateStatus){
                return  $this->sendSuccessResponse('Status updated successfully');
            }
            
           return $this->sendBadRequestResponse('Error, Status not updated');
        }

        catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function update_status($id, $status)
    {
        if ($status == 0 || $status == 1) {
            return Company::where('id', $id)->update(['status' => $status]);
        }

        return false;
    }


    public function viewCompanies($status = null){
        try {

            return $this->view_companies($status);
        
        }

        catch (QueryException $ex) {
            return $ex->getMessage();
        }
    }

    private function view_companies($status = null)
    {

            $view = DB::table('companies')->select('id','company_name', 'status');
            if(isset($status))
            {
                $view->where('status', $status);
            }

            $data = [];
            $data['status'] = true;
            $data['status_code'] = 1;
            $data['data'] = $view->get();

            return $data;

    }
}
