<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2018/12/25
 */

namespace App\Libraries\ConsistentHash;

/**
 * @description:简化Flexihash 实现的一致性hash
 * Class ConsistentHash
 * @package App\Libraries\ConsistentHash
 * @author zouhuaqiu
 * @date 2018/12/25
 */
class ConsistentHash
{
    /**
     * The number of positions to hash each target to.
     *
     * @var int
     */
    private $replicas = 64;
    /**
     * Internal counter for current number of targets.
     * @var int
     */
    private $targetCount = 0;
    /**
     * Internal map of positions (hash outputs) to targets.
     * @var array { position => target, ... }
     */
    private $positionToTarget = [];
    /**
     * Internal map of targets to lists of positions that target is hashed to.
     * @var array { target => [ position, position, ... ], ... }
     */
    private $targetToPositions = [];

    private $positionToTargetSorted = false;

    public function __construct($replicas = null)
    {
        if (!empty($replicas)) {
            $this->replicas = $replicas;
        }
    }

    public function addTarget($target, $weight = 1)
    {
        if (isset($this->targetToPositions[$target])) {
            throw new \Exception("Target '$target' already exists.");
        }
        $this->targetToPositions[$target] = [];
        // hash the target into multiple positions
        for ($i = 0; $i < round($this->replicas * $weight); ++$i) {
            $position = $this->crc32hash($target . $i);
            $this->positionToTarget[$position] = $target; // lookup
            $this->targetToPositions[$target] [] = $position; // target removal
        }
        $this->positionToTargetSorted = false;
        ++$this->targetCount;
        return $this;
    }

    public function addTargets($targets, $weight = 1)
    {
        foreach ($targets as $target) {
            $this->addTarget($target, $weight);
        }
        return $this;
    }

    public function removeTarget($target)
    {
        if (!isset($this->targetToPositions[$target])) {
            throw new \Exception("Target '$target' does not exist.");
        }
        foreach ($this->targetToPositions[$target] as $position) {
            unset($this->positionToTarget[$position]);
        }
        unset($this->targetToPositions[$target]);
        --$this->targetCount;
        return $this;
    }

    /**
     * A list of all potential targets.
     * @return array
     */
    public function getAllTargets()
    {
        return array_keys($this->targetToPositions);
    }

    public function lookup($resource)
    {
        $targets = $this->lookupList($resource, 1);
        if (empty($targets)) {
            throw new \Exception('No targets exist');
        }
        return $targets[0];
    }


    public function lookupList($resource, $requestedCount)
    {
        if (!$requestedCount) {
            throw new \Exception('Invalid count requested');
        }
        // handle no targets
        if (empty($this->positionToTarget)) {
            return [];
        }
        // optimize single target
        if ($this->targetCount == 1) {
            return array_unique(array_values($this->positionToTarget));
        }
        // hash resource to a position
        $resourcePosition = $this->crc32hash($resource);
        $results = [];
        $collect = false;
        $this->sortPositionTargets();
        // search values above the resourcePosition
        foreach ($this->positionToTarget as $key => $value) {
            // start collecting targets after passing resource position
            if (!$collect && $key > $resourcePosition) {
                $collect = true;
            }
            // only collect the first instance of any target
            if ($collect && !in_array($value, $results)) {
                $results [] = $value;
            }
            // return when enough results, or list exhausted
            if (count($results) == $requestedCount || count($results) == $this->targetCount) {
                return $results;
            }
        }
        // loop to start - search values below the resourcePosition
        foreach ($this->positionToTarget as $key => $value) {
            if (!in_array($value, $results)) {
                $results [] = $value;
            }
            // return when enough results, or list exhausted
            if (count($results) == $requestedCount || count($results) == $this->targetCount) {
                return $results;
            }
        }
        // return results after iterating through both "parts"
        return $results;
    }

    public function __toString()
    {
        return sprintf(
            '%s{targets:[%s]}',
            get_class($this),
            implode(',', $this->getAllTargets())
        );
    }

    private function sortPositionTargets()
    {
        // sort by key (position) if not already
        if (!$this->positionToTargetSorted) {
            ksort($this->positionToTarget, SORT_REGULAR);
            $this->positionToTargetSorted = true;
        }
    }

    public function md5hash($string)
    {
        return hexdec(substr(md5($string), 0, 8));
    }

    public function crc32hash($string)
    {
        return crc32($string);
    }

}