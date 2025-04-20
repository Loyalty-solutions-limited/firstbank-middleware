<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use HasFactory;
    public function templates()
    {
        return $this->hasMany(Template::class);
    }
    public function groups()
    {
        return $this->belongsToMany(EmailGroup::class, 'email_group_notifications',
         'notification_type_id', 'email_group_id')->withPivot('created_at',"email_copy");
    }
}
