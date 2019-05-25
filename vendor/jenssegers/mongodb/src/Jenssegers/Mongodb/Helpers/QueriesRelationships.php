<?php

namespace Jenssegers\Mongodb\Helpers;

use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Jenssegers\Mongodb\Eloquent\Model;

trait QueriesRelationships
{
    /**
     * Add a relationship count / exists condition to the query.
     *
     * @param  string $relation
     * @param  string $operator
     * @param  int $count
     * @param  string $boolean
     * @param  \Closure|null $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null)
    {
        if (strpos($relation, '.') !== false) {
            return $this->hasNested($relation, $operator, $count, $boolean, $callback);
        }

        $relation = $this->getRelationWithoutConstraints($relation);

        // If this is a hybrid relation then we can not use a normal whereExists() query that relies on a subquery
        // We need to use a `whereIn` query
        if ($this->getModel() instanceof Model || $this->isAcrossConnections($relation)) {
            return $this->addHybridHas($relation, $operator, $count, $boolean, $callback);
        }

        // If we only need to check for the existence of the relation, then we can optimize
        // the subquery to only run a "where exists" clause instead of this full "count"
        // clause. This will make these queries run much faster compared with a count.
        $method = $this->canUseExistsForExistenceCheck($operator, $count)
            ? 'getRelationExistenceQuery'
            : 'getRelationExistenceCountQuery';

        $hasQuery = $relation->{$method}(
            $relation->getRelated()->newQuery(), $this
        );

        // Next we will call any given callback as an "anonymous" scope so they can get the
        // proper logical grouping of the where clauses if needed by this Eloquent query
        // builder. Then, we will be ready to finalize and return this query instance.
        if ($callback) {
            $hasQuery->callScope($callback);
        }

        return $this->addHasWhere(
            $hasQuery, $relation, $operator, $count, $boolean
        );
    }

    /**
     * @param $relation
     * @return bool
     */
    protected function isAcrossConnections($relation)
    {
        return $relation->getParent()->getConnectionName() !== $relation->getRelated()->getConnectionName();
    }

    /**
     * Compare across databases
     * @param $relation
     * @param string $operator
     * @param int $count
     * @param string $boolean
     * @param Closure|null $callback
     * @return mixed
     * @throws \Exception
     */
    public function addHybridHas($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null)
    {
        $hasQuery = $relation->getQuery();
        if ($callback) {
            $hasQuery->callScope($callback);
        }

        // If the operator is <, <= or !=, we will use whereNotIn.
        $not = in_array($operator, ['<', '<=', '!=']);
        // If we are comparing to 0, we need an additional $not flip.
        if ($count == 0) {
            $not = !$not;
        }

        $relations = $hasQuery->pluck($this->getHasCompareKey($relation));

        $relatedIds = $this->getConstrainedRelatedIds($relations, $operator, $count);

        return $this->whereIn($this->getRelatedConstraintKey($relation), $relatedIds, $boolean, $not);
    }

    /**
     * Returns key we are constraining this parent model's query with
     * @param $relation
     * @return string
     * @throws \Exception
     */
    protected function getRelatedConstraintKey($relation)
    {
        if ($relation instanceof HasOneOrMany) {
            return $this->model->getKeyName();
        }

        if ($relation instanceof BelongsTo) {
            return $relation->getForeignKey();
        }

        throw new \Exception(class_basename($relation) . ' Is Not supported for hybrid query constraints!');
    }

    /**
     * @param $relation
     * @return string
     */
    protected function getHasCompareKey($relation)
    {
        if (method_exists($relation, 'getHasCompareKey')) {
            return $relation->getHasCompareKey();
        }

        return $relation instanceof HasOneOrMany ? $relation->getForeignKeyName() : $relation->getOwnerKey();
    }

    /**
     * @param $relations
     * @param $operator
     * @param $count
     * @return array
     */
    protected function getConstrainedRelatedIds($relations, $operator, $count)
    {
        $relationCount = array_count_values(array_map(function ($id) {
            return (string) $id; // Convert Back ObjectIds to Strings
        }, is_array($relations) ? $relations : $relations->flatten()->toArray()));
        // Remove unwanted related objects based on the operator and count.
        $relationCount = array_filter($relationCount, function ($counted) use ($count, $operator) {
            // If we are comparing to 0, we always need all results.
            if ($count == 0) {
                return true;
            }
            switch ($operator) {
                case '>=':
                case '<':
                    return $counted >= $count;
                case '>':
                case '<=':
                    return $counted > $count;
                case '=':
                case '!=':
                    return $counted == $count;
            }
        });

        // All related ids.
        return array_keys($relationCount);
    }
}
