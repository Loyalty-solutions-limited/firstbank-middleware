<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Usergroup;

class UserGroupsController extends Controller
{
    //
    public function fetchUserGroups(Request $request)
    {
        $get_data = Usergroup::where('status', 1)->get();

        if($get_data)
        {
            return $this->sendSuccessResponse($get_data);

        }
        else
        {
            return $this->sendBadRequestResponse("Error getting user groups");
        }

    }

    public function createUserGroup(Request $request)
    {
        $validator = Validator::make($request, [
            'name' => 'required|string|unique:user_groups, name',
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }

        $slug = trim($request->name).'-'.Str::random(6);

        $insert = new Usergroup();
        $insert->name = $request->name;
        $insert->slug = $slug;
        $insert->save();

        if($insert)
        {
            return $this->sendSuccessResponse("User Group creation successful", $insert);
        }
        else
        {
            return $this->sendBadRequestResponse($insert->errors(), "User Group not created");
        }



    }

    public function editUserGroup(Request $request, $slug)
    {
        $validator = Validator::make($request, [
            'name' => 'required|string|unique:user_groups, name',
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }

        // $slug = $request->name.'-'.Str::random(6);

        $update = Usergroup::where('slug', $slug)->update(['name' => $request->name]);

        if($update)
        {
            return $this->sendSuccessResponse("User Group edited successful", $update);
        }
        else
        {
            return $this->sendBadRequestResponse($update->errors(), "User Group not created");
        }



    }

    public function statusUserGroup(Request $request, $slug)
    {
        $validator = Validator::make($request, [
            'status' => 'required|numeric|min:0|max:1',
        ]);

        if($validator->fails())
        {
            return $this->sendBadRequestResponse($validator->errors());
        }

        $message = "";

        if($request->status == 0)
        {
            $message = "disable";
        }
        else if($request->status == 1)
        {
            $message = "enable";
        }

        $update = Usergroup::where('slug', $slug)->update(['name' => $request->name]);

        if($update)
        {
            return $this->sendSuccessResponse("User Group has been ".$message, $update);
        }
        else
        {
            return $this->sendBadRequestResponse($update->errors(), "User Group has not been ".$message);
        }

    }

}
