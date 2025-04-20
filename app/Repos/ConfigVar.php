<?php

namespace App\Repos;

use App\Interfaces\IConfigureVar;
use Illuminate\Support\Facades\DB;

class ConfigVar extends Base implements IConfigureVar
{
    // private $table_name;
    protected $table_name;
    public function __construct($table_name = "config_vars")
    {
        parent::__construct($table_name);
        $this->table_name = $table_name;
    }


    public function get_all_variables()
    {
        return DB::table($this->table_name)
                        ->leftJoin('notification_channels AS nc', "{$this->table_name}.channel_id", '=', 'nc.id')
                        ->select("{$this->table_name}.id", "{$this->table_name}.channel_id", "{$this->table_name}.key", "{$this->table_name}.required", "{$this->table_name}.created_at","nc.name")
                        ->paginate();
    }


    public function var_check($var, $channel_id)
    {
        return DB::table($this->table_name)->where('channel_id', $channel_id)->where('key', $var)->exists();
    }

    public function create_var($var,$required, $channel_id)
    {
        return DB::table($this->table_name)->insert([
            'channel_id'=> $channel_id,
            'key'=> $var,
            'required'=> $required 
        ]);
    }
}
