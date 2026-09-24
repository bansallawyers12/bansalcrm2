<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CastingBelongsTo extends BelongsTo
{
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if ($parentQuery->getQuery()->from == $query->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

        if ($this->usesIntegerCast()) {
            $foreignKey = $this->getQualifiedForeignKeyName();

            return $query->select($columns)->whereRaw(
                'CAST('.$foreignKey.' AS INTEGER) = '.$query->qualifyColumn($this->ownerKey)
            );
        }

        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
    }

    public function getRelationExistenceQueryForSelfRelation(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        if (! $this->usesIntegerCast()) {
            return parent::getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

        $query->select($columns)->from(
            $query->getModel()->getTable().' as '.$hash = $this->getRelationCountHash()
        );

        $query->getModel()->setTable($hash);

        $foreignKey = $this->getQualifiedForeignKeyName();

        return $query->whereRaw(
            'CAST('.$foreignKey.' AS INTEGER) = '.$hash.'.'.$this->ownerKey
        );
    }

    protected function usesIntegerCast(): bool
    {
        return $this->child->getConnection()->getDriverName() === 'pgsql';
    }
}
