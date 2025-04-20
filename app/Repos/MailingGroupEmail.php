<?php

namespace App\Repos;

use App\Interfaces\IMailingGroupEmail;
use Illuminate\Support\Facades\DB;

class MailingGroupEmail extends Base implements IMailingGroupEmail
{
    // private $table_name;

    public function __construct($table_name = "mailing_group_emails")
    {
        parent::__construct($table_name);

        $this->emailsTable = 'email_addresses';
    }


    /**
     * return array of emails
     *
     * @param  $groupId
     * @return array
     */
    public function getEmails(Int $groupId)
    {
        return DB::table("{$this->table_name} as mge")
            ->leftJoin($this->emailsTable, "mge.email_id", '=', "{$this->emailsTable}.id")
            ->where('group_id', $groupId)
            ->where("{$this->emailsTable}.status", 1)
            ->pluck('email')
            ->toArray();
    }
}
