<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SucursalScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user || $user->role === 'admin') {
            return;
        }

        $builder->where($model->getTable() . '.sucursal_id', $user->sucursal_id);
    }
}
