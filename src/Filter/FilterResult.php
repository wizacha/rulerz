<?php

namespace RulerZ\Filter;

/**
 * Result of a RulerZ::filter() operation.
 *
 * @see RulerZ\RulerZ::filter()
 */
class FilterResult
{
    /**
     * @var int
     */
    private $count;

    /**
     * @var callable
     */
    private $generatorProvider;

    public function __construct($count, callable $generatorProvider)
    {
        $this->count = $count;
        $this->generatorProvider = $generatorProvider;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Returns the results as a generator so that they can be iterated without having
     * to load them all in memory at once.
     *
     * Example:
     *
     *     $results = $rulerz->filter(...);
     *     foreach ($results->getResults() as $result) {
     *         ...
     *     }
     *
     * @return \Generator
     */
    public function getResults()
    {
        $callable = $this->generatorProvider;

        return $callable();
    }
}
