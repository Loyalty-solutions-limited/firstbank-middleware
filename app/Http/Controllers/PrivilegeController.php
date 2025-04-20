<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Privilege;

class PrivilegeController extends Controller
{
    //



    //support functions

    public function check_slug($slug){

        return Privilege::where('slug', $slug)->first();
    }

    private function add_privilege($ugp_id, $name)
    {
        try 
        { 
            $insert = new Privilege();
            $insert->ugp_id = $ugp_id;
            $insert->name = $name;
            $insert->status = 1;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function edit_privilege($id, $ugp_id, $name)
    {
        try 
        { 

            $edit = Privilege::where('id', $id)->update(['ugd_id' => $ugp_id, 'name'=>$name]);

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

            $edit = Privilege::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    private function view_privilege($status = null, $ugp_id = null)
    {
        try 
        { 

            $view = DB::table('privileges')
                    ->select('id','ugp_id', 'name','status');
            if(isset($status))
            {
                $view->where('status', $status);
            }
            if(isset($ugp_id))
            {
                $view->where('ugp_id', $ugp_id);
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

}
