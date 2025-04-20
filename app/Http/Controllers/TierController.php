<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Tier;

class TierController extends Controller
{
    //
    private function add_tier($name, $loyalty_program_id)
    {
        try 
        { 
            $insert = new tier();
            $insert->tier_name = $name;
            $insert->loyalty_program_id = $loyalty_program_id;
            $insert->status = 1;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function edit_tier($id, $name, $loyalty_program_id)
    {
        try 
        { 

            $edit = Tier::where('id', $id)->update(['tier_name' => $name, 'loyalty_program_id'=>$loyalty_program_id]);

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

            $edit = Tier::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    private function view_tier($status = null, $loyalty_program_id = null)
    {
        try 
        { 

            $view = DB::table('tier')
                    ->select('id', 'tier_name', 'loyalty_program_id', 'status');
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
}
