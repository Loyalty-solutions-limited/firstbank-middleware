<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotificationLog extends Model
{
    use HasFactory;
    protected $guarded = [];
    public static function injectLog(Array $data)
    {
        $entry = Self::create([
            'not_type' => $data['not_type'],
            'channel_type_id' => $data['channel_type_id'],
            'recipient' => $data['recipient'],
            'variables' => json_encode($data['variables'])
        ]);

        return request()->notLogId = $entry->id;

        // return true;
    }
    public static function updateLog(Array $data, $logId = null)
    {
        return DB::table('notification_logs')->where('id', $logId ?? request()->notLogId)
            ->update([
                'content' =>  @$data['content'],
                'channel' =>  @$data['channel'],
                'status' => ($data['status'] == true) ? 1 : 2,
                'result' => $data['result']
            ]);
    }

}
