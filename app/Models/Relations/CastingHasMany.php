<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CastingHasMany extends HasMany
{
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($this->usesIntegerCast()) {
            return $query->select($columns)->whereRaw(
                'CAST('.$this->getQualifiedForeignKeyName().' AS INTEGER) = '.$parentQuery->qualifyColumn($this->localKey)
            );
        }

        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
    }

    protected function usesIntegerCast(): bool
    {
        return $this->parent->getConnection()->getDriverName() === 'pgsql';
    }
}
