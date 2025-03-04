<?php

use Illuminate\Database\Eloquent\Model;

class StudentinfoModel extends Model
{
    protected $connection = 'mysql';
    protected $table = 'mysql.student_info';
    protected $primekey = 'id';
    public $timestamps = false;

    public static function getInfoByIdNum($idNum)
    {
        return self::where('id_num', $idNum)->first();
    }

}
