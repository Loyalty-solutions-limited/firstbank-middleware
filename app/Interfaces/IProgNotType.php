<?php

namespace App\Interfaces;

interface IProgNotType extends IBase
{
    public function check($program_id, $not_type_id);
    public function add_group($prog_not_type_id, $group_id, $email_copy);
    public function remove_group($prog_not_type_id, $group_id);
}
