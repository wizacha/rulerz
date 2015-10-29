<?php

namespace RulerZ\Executor\ArrayTarget;

use RulerZ\Context\ExecutionContext;
use RulerZ\Context\ObjectContext;
use RulerZ\Filter\FilterResult;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        $matches = [];

        foreach ($target as $row) {
            $targetRow = is_array($row) ? $row : new ObjectContext($row);

            if ($this->execute($targetRow, $operators, $parameters)) {
                $matches[] = $row;
            }
        }

        return new FilterResult(count($matches), function () use ($matches) {
            foreach ($matches as $match) {
                yield $match;
            }
        });
    }
}
