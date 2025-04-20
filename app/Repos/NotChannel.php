<?php

namespace App\Repos;


use App\Interfaces\INotChannel;
use Illuminate\Support\Facades\DB;

class NotChannel extends Base implements INotChannel
{
    // private $table_name;

    public function __construct($table_name = "notification_channels")
    {
        parent::__construct($table_name);
    }

    public function notification_channels($limit)
    {
        return DB::table('notification_channels AS not_c')
                            ->leftJoin('channel_types AS ct', 'not_c.channel_type_id', '=', 'ct.id')
                            ->select('not_c.id','not_c.name','not_c.description', 'not_c.code','ct.name', 'not_c.created_at')
                            ->paginate($limit);
    }
}
