<?php 
namespace App\Services;
use App\Services\EnrolmentMigrationService as EMS;
use App\Services\TransactionMigrationService as TMS; 

class PushDataService{
    
    public function __construct()
    {
        
    }

    public static function migrate(){
        
        EMS::migrateEnrolments1();
        TMS::migrateTransaction1();
        
    }

    public static function trigger(){
        return self::migrate();
    }
    
}
?>