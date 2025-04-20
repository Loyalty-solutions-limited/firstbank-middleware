<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailGroup extends Model
{
    use HasFactory;
    public function addresses()
    {
        return $this->belongsToMany(EmailAddress::class, 'email_group_addresses',
         'email_group_id', 'email_address_id')->withPivot('created_at');
    }

    public function notifications()
    {
        return $this->belongsToMany(NotificationType::class, 'email_group_notifications',
         'email_group_id', 'notification_type_id')->withPivot('created_at',"email_copy");
    }
}
