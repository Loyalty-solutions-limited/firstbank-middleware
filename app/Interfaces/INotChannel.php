<?php

namespace App\Interfaces;

interface INotChannel extends IBase
{
   public function notification_channels($limit);
}
