<?php

namespace App\Repos;

use App\Interfaces\IChannel;
use Illuminate\Support\Facades\DB;

class Channel extends Base implements IChannel
{
    // private $table_name;

    public function __construct($table_name = "channel_types")
    {
        parent::__construct($table_name);
    }

   
}
