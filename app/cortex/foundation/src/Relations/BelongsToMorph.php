<?php

declare(strict_types=1);

namespace Cortex\Foundation\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BelongsToMorph extends BelongsTo
{
    /**
     * The name of the polymorphic relation.
     *
     * @var string
     */
    protected $morphName;

    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType;

    /**
     * Create a new belongs to relationship instance.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model   $parent
     * @param string                                $name
     * @param string                                $type
     * @param string                                $foreignKey
     * @param string                                $ownerKey
     * @param string                                $relation
     *
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $name, $type, $foreignKey, $ownerKey, $relation)
    {
        $this->morphName = $name;
        $this->morphType = $type;

        parent::__construct($query, $parent, $foreignKey, $ownerKey, $relation);
    }

    /**
     * Add the constraints for a relationship query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Builder $parent
     * @param array|mixed                           $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRelationQuery(Builder $query, Builder $parent, $columns = ['*'])
    {
        $table = $this->getParent()->getTable();
        $query = parent::getRelationQuery($query, $parent, $columns);

        return $query->where("{$table}.{$this->morphType}", '=', $this->morphName);
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        if ($this->getParent()->{$this->morphType} === $this->morphName) {
            return $this->query->first();
        }
    }

    /**
     * Get the polymorphic relationship columns.
     *
     * @param string $name
     * @param string $type
     * @param string $id
     *
     * @return array
     */
    protected static function getMorphs($name, $type, $id)
    {
        $type = $type ?: $name.'_type';

        $id = $id ?: $name.'_id';

        return [$type, $id];
    }

    /**
     * Define an inverse morph relationship.
     *
     * @param Model  $parent
     * @param string $related
     * @param string $name
     * @param string $type
     * @param string $id
     * @param string $otherKey
     * @param string $relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public static function build(Model $parent, $related, $name, $type = null, $id = null, $otherKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            [$current, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            $relation = $caller['function'];
        }

        $morphName = array_get(array_flip(Relation::morphMap()), $related, $related);

        [$type, $id] = self::getMorphs(snake_case($name), $type, $id);

        $instance = new $related();

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $query = $instance->newQuery();

        $otherKey = $otherKey ?: $instance->getKeyName();

        return new self($query, $parent, $morphName, $type, $id, $otherKey, $relation);
    }
}
