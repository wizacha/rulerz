<?php

namespace RulerZ\Executor\Eloquent;

use Illuminate\Database\Query\Builder as QueryBuilder;

use RulerZ\Context\ExecutionContext;
use RulerZ\Filter\FilterResult;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * @inheritDoc
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        $query = !$target instanceof QueryBuilder ? $target->getQuery() : $target;
        $sql   = $this->execute($target, $operators, $parameters);

        $query->whereRaw($sql, $parameters);

        $results = $query->get();

        return new FilterResult(count($results), function () use ($results) {
            foreach ($results as $result) {
                yield $result;
            }
        });
    }
}
