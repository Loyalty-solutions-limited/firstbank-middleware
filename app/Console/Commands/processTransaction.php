<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class processTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trigger:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Spool Un-processed Transaction Records';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = "https://172.16.26.8";

        return Log::info(self::make_curl($url . "/runcron2?API_flag=stage_final7"));
    }

    public static function make_curl($url)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: 13b196c5b4060ad51e269b0471ab935c'
        ),
        ));

        $response = curl_exec($curl);
        echo "..." . curl_error($curl);
        exit;
        curl_close($curl);
        return $response;
    }
}