<?php

namespace App\Repos;

use App\Interfaces\INotType;
use Illuminate\Support\Facades\DB;

class NotType extends Base implements INotType
{
    protected $table_name;

    public function __construct($table_name = "notification_types")
    {
        parent::__construct($table_name);
    }


    public function program_notification_types($programm_id, $limit)
    {
        return DB::table('program_notification_types AS P_NT')
            ->leftJoin('notification_types AS NT', 'P_NT.not_type_id', '=', 'NT.id')
            ->leftJoin('channel_types', 'NT.channel_type_id', '=', 'channel_types.id')
            ->where('P_NT.program_id', $programm_id)
            ->select('NT.name', 'NT.description', 'NT.slug', 'channel_types.name as channel_name', 'NT.status')
            ->paginate($limit);
    }

    public function program_not_type_dropdown($programm_id)
    {
        return DB::table('program_notification_types AS P_NT')
            ->leftJoin('notification_types AS NT', 'P_NT.not_type_id', '=', 'NT.id')
            ->where('P_NT.program_id', $programm_id)
            ->where('NT.status', 1)
            ->select('NT.name', 'NT.slug')
            ->orderByDesc('P_NT.created_at')
            ->get();
    }

    public function notification_types($limit)
    {
        return DB::table('program_notification_types AS P_NT')
            ->leftJoin('notification_types AS NT', 'P_NT.not_type_id', '=', 'NT.id')
            ->leftJoin('channel_types', 'NT.channel_type_id', '=', 'channel_types.id')
            ->select('NT.name', 'NT.description', 'NT.slug', 'channel_types.name as channel_name', 'NT.status')
            ->paginate($limit);
    }
}
