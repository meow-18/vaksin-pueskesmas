<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const ADMIN = 'Admin';
    public const APOTEKER = 'Petugas Apoteker';

    protected $table = 'role';
    protected $primaryKey = 'id_role';
    public $timestamps = false;
    protected $guarded = [];
}