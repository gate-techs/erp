<?php

namespace Modules\AuditLog\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CrossDatabaseMorphTo extends MorphTo
{
    /**
     * Create a new model instance by type.
     * Ensures all morphTo models always execute on the main ERP database connection.
     *
     * @param  string  $type
     * @return Model
     */
    public function createModelByType($type)
    {
        $class = Model::getActualClassNameForMorph($type);
        $defaultConnection = config('database.default', 'mysql');

        return tap(new $class, function ($instance) use ($defaultConnection) {
            $instance->setConnection($defaultConnection);
        });
    }

    /**
     * Get the results of the relationship for lazy loading.
     *
     * @return mixed
     */
    public function getResults()
    {
        $type = $this->morphType;
        $foreignKey = $this->foreignKey;

        if (empty($this->parent->{$type}) || is_null($this->parent->{$foreignKey})) {
            return null;
        }

        $class = Model::getActualClassNameForMorph($this->parent->{$type});
        $defaultConnection = config('database.default', 'mysql');

        $instance = (new $class)->setConnection($defaultConnection);

        $query = $instance->newQuery();

        if (config('activitylog.subject_returns_soft_deleted_models') && method_exists($instance, 'withTrashed')) {
            $query->withTrashed();
        }

        $ownerKey = $this->ownerKey ?? $instance->getKeyName();

        return $query->where($ownerKey, $this->parent->{$foreignKey})->first();
    }
}
