<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\HybridRelations;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HybridRelations;

    protected $connection = 'mongodb';
    protected $collection = 'roles';
}
