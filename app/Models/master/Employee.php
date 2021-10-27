<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'dbo.EmployeeInformation';

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
