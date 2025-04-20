<?php

namespace App\Http\Controllers;

use App\Models\EmailReportLog;
use Illuminate\Http\Request;

class EmailReportController extends Controller
{
    //

    public function index(){
        $logData = EmailReportLog::limit(500)->orderBy('id', 'desc')->get();
        $data = [];
        $data['status'] = true;
        $data['status_code'] = 1;
        $data['data'] = $logData;
        return $data;
    }
}