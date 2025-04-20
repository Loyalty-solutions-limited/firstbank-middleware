<?php



namespace App\Http\Controllers;
ini_set('memory_liit', '-1');
ini_Set('maximum_execuion_time', '1000');


use App\Services\FetchStatsService;

use Illuminate\Http\Request;

use App\Models\Enrollment;

use App\Models\EnrolReportLog;

use App\Models\PendingEmails;

use App\Models\Transaction;

use App\Models\TransactionReportLog;

use Illuminate\Support\Facades\DB;



class StatsController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

            if (!$request->session()->get('is_allowed')){

                return redirect('/allow_me');

            }

            $request->enrol_offset?$request->enrol_offset=$request->enrol_offset:$request->enrol_offset=1;

            $request->trans_offset?$request->trans_offset=$request->trans_offset:$request->trans_offset=1;

            $request->trans_log_offset?$request->trans_log_offset=$request->trans_log_offset:$request->trans_log_offset=1;

            $request->enrol_log_offset?$request->enrol_log_offset=$request->enrol_log_offset:$request->enrol_log_offset=1;

           

       

            $unique_count = DB::table('enrollments')

                 ->select('loyalty_number', DB::raw('count(*) as total'))

                 ->groupBy('loyalty_number')

                 ->get();

 

            //$products = $art->products->skip(10)->take(10)->get();

            $fetch_all_enrolments = Enrollment::skip(($request->enrol_offset - 1)* 200)->take(200)->get();

           // $count_all_enrollments = Enrollment::all();

            $count_all_transactions = Transaction::where('id', '>', 0)->limit(100)->get();;

            //print_r($count_all_enrolments);

            $fetch_all_transactions = Transaction::skip(($request->trans_offset - 1) * 200)->take(200)->get();

            $count_all_mails = PendingEmails::where('id', '>', 0)->limit(100)->get();

           

            $count_migrated_enrolments = Enrollment::where('enrollment_status', 1)->count();

            $count_pending_enrolments = Enrollment::where('enrollment_status', 0)->count();

            $count_failed_enrolments = Enrollment::where('enrollment_status', 0)->where('tries', '>', 4)->count();

            $reports = TransactionReportLog::skip(($request->trans_log_offset - 1) * 200)->take(200)->get();

            $reports2 = EnrolReportLog::skip(($request->enrol_log_offset-1) * 100)->take(100)->get();

            $report2_count = EnrolReportLog::where('id', '>', 0)->count();

            return view('stats.stats-view', ['enrolment_data'=>$fetch_all_enrolments, 'transaction_data'=>$fetch_all_transactions, 'mails'=>$count_all_mails, 'reports'=>$reports, 'reports2_count'=> $report2_count, 'failed_enrollments'=>$count_failed_enrolments, 'reports2'=>$reports2,'successful_enrollments'=>$count_migrated_enrolments, 'pending_enrollments'=>$count_pending_enrolments, 'unique_customer_count'=>$unique_count]);

       

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