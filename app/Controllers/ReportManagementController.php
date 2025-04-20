<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportManagementService;

class ReportManagementController extends Controller
{
    //

    public function index(){
        
        $reportService = new ReportManagementService();
        return $reportService->generateTransactionReport();
        
    }
}