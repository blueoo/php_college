<?php

namespace Jenssegers\Mongodb\Schema;

use Closure;
use Jenssegers\Mongodb\Connection;

class Builder extends \Illuminate\Database\Schema\Builder
{
    /**
     * @inheritdoc
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public function hasColumn($table, $column)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function hasColumns($table, array $columns)
    {
        return true;
    }

    /**
     * Determine if the given collection exists.
     *
     * @param  string $collection
     * @return bool
     */
    public function hasCollection($collection)
    {
        $db = $this->connection->getMongoDB();

        foreach ($db->listCollections() as $collectionFromMongo) {
            if ($collectionFromMongo->getName() == $collection) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function hasTable($collection)
    {
        return $this->hasCollection($collection);
    }

    /**
     * Modify a collection on the schema.
     *
     * @param  string $collection
     * @param  Closure $callback
     * @return bool
     */
    public function collection($collection, Closure $callback)
    {
        $blueprint = $this->createBlueprint($collection);

        if ($callback) {
            $callback($blueprint);
        }
    }

    /**
     * @inheritdoc
     */
    public function table($collection, Closure $callback)
    {
        return $this->collection($collection, $callback);
    }

    /**
     * @inheritdoc
     */
    public function create($collection, Closure $callback = null)
    {
        $blueprint = $this->createBlueprint($collection);

        $blueprint->create();

        if ($callback) {
            $callback($blueprint);
        }
    }

    /**
     * @inheritdoc
     */
    public function drop($collection)
    {
        $blueprint = $this->createBlueprint($collection);

        return $blueprint->drop();
    }

    /**
     * @inheritdoc
     */
    protected function createBlueprint($collection, Closure $callback = null)
    {
        return new Blueprint($this->connection, $collection);
    }
}
