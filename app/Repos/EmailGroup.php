<?php

namespace App\Repos;

use App\Interfaces\IEmailGroup;
use Illuminate\Support\Facades\DB;

class EmailGroup extends Base implements IEmailGroup
{
    // private $table_name;
    protected $table_name;

    public function __construct($table_name = "mailing_groups")
    {
        $this->table_name = $table_name;
        parent::__construct($table_name);
    }

    public function program_groups($program_id, $limit=null)
    {
        return DB::table($this->table_name)
                    ->where("{$this->table_name}.program_id", $program_id)
                    ->leftJoin('loyalty_programs', "{$this->table_name}.program_id", '=', 'loyalty_programs.id')
                    ->select("{$this->table_name}.id","{$this->table_name}.name", "loyalty_programs.name as progam_name")
                    ->paginate($limit ?? 15);

    }

    public function check_mail($group_id, $email)
    {
        return DB::table('mailing_group_emails')->where('group_id', $group_id)->where('email_id', $email)->exists();
    }

    public function add_email($email, $group_id)
    {
        
       return DB::table('mailing_group_emails')->insert([
            'email_id'=> $email,
            'group_id'=> $group_id
       ]);
    }

    public function remove_email($email, $group_id)
    {
        return DB::table('mailing_group_emails')->where('email_id', $email)->where('group_id', $group_id)->delete();
    }

    public function emails($group_id, $limit=null)
    {
        return DB::table('mailing_group_emails AS ge')
                            ->where('ge.group_id', $group_id)
                            ->leftJoin('email_addresses as email', 'ge.email_id', '=', 'email.id')
                            ->leftJoin('loyalty_programs', 'email.program_id', '=', 'loyalty_programs.id')
                            ->select('email.id', 'email.email', 'email.status', 'loyalty_programs.name')
                            ->paginate($limit ?? 15);
    }

    public function email_dropdown($group_id)
    {
        return DB::table('mailing_group_emails AS ge')
                        ->where('ge.group_id', $group_id)
                        ->leftJoin('email_addresses as email', 'ge.email_id', '=', 'email.id')
                        ->select('email.id', 'email.email')
                        ->get();
    }

    public function belongToprogrm($program_id, $group_id)
    {
        return DB::table($this->table_name)->where('id', $group_id)->where('program_id', $program_id)->first();
    }

}
