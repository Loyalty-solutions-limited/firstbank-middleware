<?php

namespace App\Repos;

use App\Interfaces\INotTypeMailGroup;
use Illuminate\Support\Facades\DB;

class NotTypeMailGroup extends Base implements INotTypeMailGroup
{
    // private $table_name;

    public function __construct($table_name = "prog_not_type_mail_groups")
    {
        parent::__construct($table_name);
    }


    public function getGroups(Object $notType)
    {
        return DB::table("{$this->table_name}")
            ->where('not_type_id', $notType->id)
            ->get()
            ->groupBy('email_copy')
            ->map(function($item){
                return $item->map(function($item) {
                    return $item->group_id;
                });
            })->toArray();
    }
}
