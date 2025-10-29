<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Spatie\Permission\Models\Role as SpatieRole;
class Role extends SpatieRole
{
    use HasFactory;

    public const ADMIN = 'ADMIN';
    public const COACH  = 'COACH';
    public const CLIENT = 'CLIENT';

}
