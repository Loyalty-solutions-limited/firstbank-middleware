<?php

namespace App\Imports;

use App\Models\Enrollment;
//use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsFailures;
//use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EnrollmentsImport implements ToModel// SkipsOnError, WithValidation, SkipsOnFailure
{
    //use Importable SkipsErrors, SkipsFailures;//the skip on failures trait submits all record to the db apart from the one with error(s)
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(Array $row)
    {

          DB::table('enrollments')->insert([
            'first_name' => $row[0],
            'middle_name' =>$row[1],
            'last_name' => $row[2],
            'branch_id' => $row[3],
            'loyalty_program_id' => $row[4],
            'email' => $row[5],
            'member_reference' => $row[6],
            'password' => Hash::make('password'),
        ]);

    }

    public function rules(): array
    {
        return [
        '*.5' => ['email','required','unique:enrollments,email'],
        ];
    }


}

