<?php

namespace App\Interfaces;

interface INotType extends IBase
{
    public function program_notification_types($programm_id, $limit);
    public function program_not_type_dropdown($programm_id);
    public function notification_types($limit);
}
