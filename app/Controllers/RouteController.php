<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Route;

class RouteController extends Controller
{
    //
    private function add_route($name)
    {
        try 
        { 
            $insert = new Route();
            $insert->route_name = $name;
            $insert->status = 1;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function edit_route($id, $name)
    {
        try 
        { 

            $edit = Route::where('id', $id)->update(['route_name' => $name]);

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

            $edit = Route::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    private function view_route($status = null)
    {
        try 
        { 

            $view = DB::table('routes')
                    ->select('id', 'route_name', 'status');
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
