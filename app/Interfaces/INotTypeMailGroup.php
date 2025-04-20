<?php

namespace App\Interfaces;

interface INotTypeMailGroup extends IBase
{
    public function getGroups(Object $notType);
}
