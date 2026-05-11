<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\HybridRelations;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use HybridRelations;

    protected $connection = 'mongodb';
    protected $collection = 'permissions';
}
