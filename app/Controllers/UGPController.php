<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Models\User_group_privilege;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UGPController extends Controller
{
    private $priviledges;
    public function __construct()
    {
        $this->priviledges = resolve(PrivilegeController::class);
        
    }

    public function get_priviledges(Request $request){
        $user_group = Auth::user()->user_group_id;
        //dd($user_group);
        $get_data = $this->get_user_privileges($user_group);
        return $this->sendSuccessResponse('Success', $get_data);


    }

    public function get_priviledge_routes(Request $request){
        $user_group = Auth::user()->user_group_id;

        $validator = Validator::make($request->all(), [
            'slug' => 'required',
            
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }
        $check_data = $this->priviledges->check_slug($request->slug);
        if($check_data){

            //dd($check_data);
            $send_data = $this->get_user_priviledge_routes($user_group, $check_data->id);
            $get_data['priviledge'] = $check_data->name;
            $get_data['access'] = $send_data;
            return $this->sendSuccessResponse("Success", $get_data);

        } else {

            return $this->sendBadRequestResponse([], "Privildege Not found");
        }

    }
    //support functions
    private function get_user_privileges($user_group){

        return User_group_privilege::select('user_group_privileges.id', 'priviledges.name','priviledges.slug', 'create', 'read', 'edit','delete')
        ->where('usergroup_id', $user_group)
        ->leftjoin('priviledges', 'priviledges.id', 'user_group_privileges.priviledge_id')
        ->get();


    }

    private function get_user_priviledge_routes($user_group, $priviledge){

        return User_group_privilege::select('create', 'read','edit','delete')
        ->where('usergroup_id', $user_group)
        ->where('user_group_privileges.priviledge_id', $priviledge)
        ->first();
    }


    private function add_ugp($usergroup_id, $route_id, $create, $read, $edit, $delete)
    {
        try 
        { 
            $insert = new User_group_privilege();
            $insert->usergroup_id = $usergroup_id;
            $insert->route_id = $route_id;
            $insert->create = $create;
            $insert->read = $read;
            $insert->edit = $edit;
            $insert->delete = $delete;
            $insert->save();

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }

    }


    private function edit_ugp($id, $usergroup_id, $route_id, $create, $read, $edit, $delete)
    {
        try 
        { 

            $edit = User_group_privilege::where('id', $id)->update(['usergroup_id' => $usergroup_id, 'route_id'=>$route_id, 'create'=>$create, 'read' =>$read, 'edit'=> $edit, 'delete'=>$delete]);

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

            $edit = User_group_privilege::where('id', $id)->update(['status' => $status]);

            return true;

        }
        catch(QueryException $ex)
        {
            return $ex->getMessage();
        }
        
    }

    private function view_ugp($status = null, $route_id = null)
    {
        try 
        { 

            $view = DB::table('user_group_privilege')
                    ->select('id', 'usergroup_id', 'route_id', 'create', 'read', 'edit', 'delete', 'status');
            if(isset($status))
            {
                $view->where('status', $status);
            }

            if(isset($route_id))
            {
                $view->where('route_id', $route_id);
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
