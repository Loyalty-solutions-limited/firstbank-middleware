<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    //

    private function add_branches($company_id, $branch_code, $name)
    {
        try 
        { 
            $insert = new Branch();
            $insert->company_id = $company_id;
            $insert->branch_code = $branch_code;
            $insert->branch_name = $name;
            $insert->status = 1;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function edit_branches($id, $company_id, $branch_code, $branch_name)
    {
        try 
        { 

            $edit = Branch::where('id', $id)->update(['company_id' => $company_id, 'branch_code'=>$branch_code, 'branch_name'=>$branch_name]);

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

            $edit = Branch::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    public function view_branch($status = null)
    {
        try 
        { 

            $view = DB::table('branches')
                    ->select('id','company_id', 'branch_code', 'branch_name', 'status');
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
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
    }
    
    public function view_branches(){

        $view = DB::table('branches')->where('status', '=', 1)
        ->select('id', 'branch_code', 'branch_name');
        return $view->get()->toJson();
        
    }

}