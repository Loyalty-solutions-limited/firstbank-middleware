<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendMailController extends Controller
{
    public function sendMailNew(Request $request)
    {
        return $request->all();
    }
}