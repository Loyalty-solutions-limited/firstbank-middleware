<?php

namespace App\Interfaces;

interface IProgram extends IBase
{
    public function program_channel($program_id);
    public function client_programs($client_id);
}
