<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface INotTypeService
{
    public function checkType(Request $request, $bulk = 0);
    public function validateRecipient(String $recipient);
}
