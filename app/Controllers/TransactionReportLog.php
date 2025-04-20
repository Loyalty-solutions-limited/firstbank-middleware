<?php

namespace App\Http\Controllers;

use App\Models\EnrolReportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionReportLog extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->filter){
            $sql = "select * from transaction_report_logs where transaction_reference= $request->filter OR member_reference=$request->filter";
        }else{
            $sql = 'select * from transaction_report_logs where 1 order by id desc limit 500 ';
            }
        
        $transaction_logs = DB::select($sql);
       
        $data = [];
            $data['status'] = true;
            $data['status_code'] = 1;
            $data['data'] = $transaction_logs;
        return $data;
        
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
    public function edit(Request $request, $id)
    {
        //
        Log::info;
        $transaction_logs = DB::select("select * from transaction_report_logs where transaction_reference= $request->data OR member_reference=$request->data");
        $data = [];
        $data['status'] = true;
        $data['status_code'] = 1;
        $data['data'] = $transaction_logs;
        return $data;
        //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
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