<?php

namespace App\Repos;

use App\Interfaces\IProgram;
use Illuminate\Support\Facades\DB;

class Program extends Base implements IProgram
{
    protected $table_name;

    public function __construct($table_name = "loyalty_programs")
    {
        parent::__construct($table_name);
        $this->table_name = $table_name;
    }

    public function program_channel($program_id)
    {
        return DB::table('program_channels AS pc')
            ->leftJoin('loyalty_programs', 'pc.program_id', '=', 'loyalty_programs.id')
            ->leftJoin('channel_types', 'pc.channel_type_id', '=', 'channel_types.id')
            ->where('pc.program_id', $program_id)
            ->select('loyalty_programs.name', 'channel_types.channel_name', 'pc.created_at')
            ->get();
    }

    

    public function client_programs($client_id)
    {
        return DB::table($this->table_name.'AS P')
                            ->leftJoin('companies', 'companies.id', '=', 'P.company_id')
                            ->where('companies.id', '=', $client_id)
                            ->select('companies.company_name', 'P.name','P.slug','P.status','P.created_at')
                            ->orderByDesc('id')
                            ->get();
    }
    
    
}

