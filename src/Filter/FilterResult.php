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
     * @return \Generator
     */
    public function getGenerator()
    {
        $callable = $this->generatorProvider;

        return $callable();
    }
}
