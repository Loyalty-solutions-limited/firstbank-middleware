<?php

namespace App\Http\Controllers;

use App\Services\FetchStatsService;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\PendingEmails;
use App\Models\Transaction;
use App\Models\TransactionReportLog;

class StatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //print_r($request->all());
        //
        // $statsArray = new FetchStatsService($request->prog_slug);
        // $statsArray->getUsersCount();
        // $statsArray->getCustomersCount();
        // $statsArray->getTransactionsCount();
        // print_r( $statsArray->showData());
        
            $count_all_enrolments = Enrollment::all();
            //print_r($count_all_enrolments);
            $count_all_transactions = Transaction::orderBy('id', 'desc')->limit(500)->get();
            $count_all_mails = PendingEmails::all();
            
            $count_migrated_enrolments = Enrollment::where('enrollment_status', 1)->count();
            $count_pending_enrolments = Enrollment::where('enrollment_status', 0)->count();
            $reports = TransactionReportLog::orderBy('id', 'desc')->limit(1000)->get();
            return view('stats.stats-view', ['enrolment_data'=>$count_all_enrolments, 'transaction_data'=>$count_all_transactions, 'mails'=>$count_all_mails, 'reports'=>$reports]);
        
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