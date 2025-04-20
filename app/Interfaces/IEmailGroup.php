<?php

namespace App\Interfaces;

interface IEmailGroup extends IBase
{
    public function program_groups($program_id, $limit);
    public function check_mail($group_id, $email);
    public function add_email($email, $group_id);
    public function remove_email($email, $group_id);
    public function emails($group_id, $limit);
    public function email_dropdown($group_id);
    public function belongToprogrm($program_id, $group_id);
}
