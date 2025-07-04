<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\EmailDispatcher;
use App\Services\PushDataService;
use App\Services\CreateAccessKeyService;
use App\Services\EnrolmentMigrationService;
use App\Services\TransactionMigrationService;

class MigrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // if(!($request->id) || !($request->API_flag))
        // {
        //     return response()->json([
        //         "message" => "Please, provide an API flag and the last row id",
        //         "status" => false
        //     ],Response::HTTP_EXPECTATION_FAILED);
        // }

       if ($request->API_flag=='enrol') {
        return EnrolmentMigrationService::migrateEnrolments1();
       }
      else if ($request->API_flag == 'stran') {
        return TransactionMigrationService::migrateTransaction1();
      }
	   elseif ($request->API_flag == 'stran2') {
        return TransactionMigrationService::migrateTransaction2();
      }
       else if ($request->API_flag == 'mailer'){
        return EmailDispatcher::sendPendingEnrolmentEmails();
        }else if($request->API_flag == 'key'){
            return CreateAccessKeyService::index();
        }else if($request->API_flag == 'stage_final7'){

			return TransactionMigrationService::rollbackTransactions();
		} else if($request->API_flag == 'staging'){

			return TransactionMigrationService::rollbackTransactions();
		}
        else{
            return TransactionMigrationService::migrateTransaction1();
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
		return TransactionMigrationService::runSpecificTransactions($request->data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}