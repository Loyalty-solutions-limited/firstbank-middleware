<?php

namespace App\Repos;

use App\Interfaces\IBase;
use App\Interfaces\IVariable;
use Illuminate\Support\Facades\DB;

class Variable extends Base implements IVariable
{
    // private $table_name;

    public function __construct($table_name = "variables")
    {
        parent::__construct($table_name);
    }

    public function notification_type_variables($not_type_id)
    {
        return DB::table('notification_variables AS not_var')
                        ->leftJoin('variables AS var', 'not_var.variable_id', '=', 'var.id')
                        ->where('not_var.not_type_id', $not_type_id)
                        ->select('var.id','var.name', 'var.description', 'var.status')
                        ->orderByDesc('not_var.created_at')
                        ->get();
    }
}
