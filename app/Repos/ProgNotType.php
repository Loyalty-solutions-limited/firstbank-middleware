<?php

namespace App\Repos;

use App\Interfaces\IProgNotType;
use Illuminate\Support\Facades\DB;

class ProgNotType extends Base implements IProgNotType
{
    protected $table_name;

    public function __construct($table_name = "program_notification_types")
    {
        parent::__construct($table_name);
    }


    public function check($program_id, $not_type_id)
    {
        return DB::table('program_notification_types')->where('program_id', $program_id)->where('not_type_id', $not_type_id)->first();
    }


    public function add_group($prog_not_type_id, $group_id, $email_copy)
    {
        return DB::table('prog_not_type_mail_groups')->insert([
            'prog_not_type_id'=> $prog_not_type_id,
            'group_id'=> $group_id,
            'email_copy'=> $email_copy
        ]);
    }

    public function remove_group($prog_not_type_id, $group_id)
    {
        return DB::table('prog_not_type_mail_groups')->where('prog_not_type_id', $prog_not_type_id)->where('group_id', $group_id)->delete();
    }

}
