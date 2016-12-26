<?php
namespace App\Models;

// use App\Models\EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends EloquentModel
{
    // 软删除
    use SoftDeletes;

    protected $table = 'users';

    // 允许批量赋值的字段
    protected $fillable = ['name', 'username'];

}