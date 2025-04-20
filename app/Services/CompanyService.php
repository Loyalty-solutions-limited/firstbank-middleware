<?php

namespace App\Services;

use App\Models\Company;

class CompanyService{
    
    public function __construct($companyID)
    {
       $this->company_id = $companyID; 
    }
    
    public  function getCompanyDetails():array|object
    {
       $details = Company::where('id', $this->company_id);
       
       return $details;
    }
    
}


?>