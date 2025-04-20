<?php

namespace App\Interfaces;

interface IConfigureVar extends IBase
{
    public function get_all_variables();
    public function var_check($var, $channel_id);
    public function create_var($var, $required, $channel_id);
}
