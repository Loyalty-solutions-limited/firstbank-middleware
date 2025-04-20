<?php

namespace App\Http\Controllers;

use App\Interfaces\INotType;
use App\Interfaces\IVariable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VariableController extends Controller
{
    public $variable, $notType;
    public $limit;

    public function __construct(IVariable $variable, INotType $notType)
    {
        $this->variable = $variable;
        $this->notType = $notType;
        $this->limit = 15;
    }

    public function all()
    {
        $for_dropdown = request()->query('dropdown');

        if (!is_null($for_dropdown) && ($for_dropdown == true)) {

            $variables = $this->variable->getItem(['status'=> 1],['id', 'name']);

        } else {
            if (request()->query('limit')) {
                $this->limit = request()->query('limit');
            }
            $variables = $this->variable->selectAll(['id', 'name','description', 'status', 'created_at'], $this->limit);
           
        }

        return $this->sendSuccessResponse( '',$variables);

    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|string',
            'description'=> 'nullable|string',
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $store = $this->variable->updateOrInsert([
            'name'=> $request->get('name')
        ],[ 'description'=> $request->get('description')]);

        if(is_null($store)){
            return $this->sendBadRequestResponse('Error creating variable');
        }
        if(!$store){
            return $this->sendBadRequestResponse('Variable already already exist in system');
        }
        return $this->sendSuccessResponse('Variable created successfully');
    }

    public function link_to_not_type(Request $request, $variable_id)
    {
        $validator = Validator::make($request->all(), [
            'notification_slugs'=> 'required|array'
        ]);

        if($validator->fails()){
            return $this->sendBadRequestResponse($validator->errors()->first());
        }
        
        $variable = $this->variable->findItem(['id'=> $variable_id]);
        
        if(is_null($variable)){
            return $this->sendBadRequestResponse("Variable not found");
        }

        $types = $request->get('notification_slugs');
        DB::beginTransaction();
        try {
            foreach ($types as $type) {
                $type_id = $this->notType->findItem(['slug' => $type]);
                if(!$type_id){
                    throw new \Exception("notification type do not exist with the slug". $type);
                }
                DB::table('notification_variables')->insert([
                    'variable_id' => $variable->id,
                    'not_type_id' => $type_id->id
                ]);
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->sendBadRequestResponse($ex->getMessage());
        }

      
        return $this->sendSuccessResponse('Notification types Linked successfully','');
    }

    public function unlink_from_not_type(Request $request, $variable_id)
    {
        $validator = Validator::make($request->all(), [
            'notification_slugs' => 'required|array'
        ]);

        if ($validator->fails()) {
            return $this->sendBadRequestResponse($validator->errors()->first());
        }

        $variable = $this->variable->findItem(['id' => $variable_id]);
        
        if (is_null($variable)) {
            return $this->sendBadRequestResponse("Variable not found");
        }

        $types = $request->get('notification_slugs');
        DB::beginTransaction();
        try {

            foreach ($types as $type) {
                $type_id = $this->notType->findItem(['slug' => $type]);
                if(!$type_id){
                    throw new \Exception("notification type do not exist with the slug $type");
                }
                DB::table('notification_variables')->where('variable_id', $variable->id)->where('not_type_id', $type_id->id)->delete();
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return $this->sendBadRequestResponse($ex->getMessage());
        }
       
        return $this->sendSuccessResponse('Notification types Unlinked successfully');
    }


    public function delete($variable_id)
    {
        DB::beginTransaction();

        try {
            $variable = $this->variable->deleteItem(['id' => $variable_id]);
            
            DB::table('notification_variables')->where('variable_id', $variable_id)->delete();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->sendBadRequestResponse($ex->getMessage());
        }
      
        if(!$variable){
            return $this->sendBadRequestResponse('Error deleting variable. Variable not found');
        }

        return $this->sendSuccessResponse('Variable deleted successfully');
    }


}
