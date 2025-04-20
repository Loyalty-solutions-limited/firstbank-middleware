<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class PermissionController extends Controller
{
    //

    private function add_permission($privilege_id, $route_name, $url)
    {
        try 
        { 
            $insert = new Permission();
            $insert->privilege_id = $privilege_id;
            $insert->route_name = $route_name;
            $insert->url = $url;
            $insert->status = 1;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function edit_permission($id, $privilege_id, $route_name, $url)
    {
        try 
        { 

            $edit = Permission::where('id', $id)->update(['privilege_id' => $privilege_id, 'route_name'=>$route_name, 'url'=>$url]);

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

            $edit = Permission::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    private function view_permission($status = null, $privilege_id = null)
    {
        try 
        { 

            $view = DB::table('permissions')
                    ->select('id','privilege_id', 'route_name', 'url','status');
            if(isset($status))
            {
                $view->where('status', $status);
            }
            if(isset($privilege_id))
            {
                $view->where('privilege_id', $privilege_id);
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
