<?php

namespace App\Repos;

use App\Interfaces\IEmail;
use Illuminate\Support\Facades\DB;

class Email extends Base implements IEmail
{
    // private $table_name;
    protected $table_name;

    public function __construct($table_name = "email_addresses")
    {
        $this->table_name = $table_name;
        parent::__construct($table_name);
    }

    

   
    public function program_emails($program_id)
    {
        return DB::table($this->table_name)
            ->where("{$this->table_name}.program_id", $program_id)
            ->leftJoin('loyalty_programs', "{$this->table_name}.program_id", '=', 'loyalty_programs.id')
            ->select("{$this->table_name}.id", "{$this->table_name}.email", "{$this->table_name}.status",)
            ->paginate();
    }
}
