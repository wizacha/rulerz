<?php

namespace RulerZ\Executor\DoctrineQueryBuilder;

use RulerZ\Context\ExecutionContext;
use RulerZ\Filter\FilterResult;

trait FilterTrait
{
    abstract protected function execute($target, array $operators, array $parameters);

    /**
     * {@inheritDoc}
     */
    public function filter($target, array $parameters, array $operators, ExecutionContext $context)
    {
        /** @var \Doctrine\ORM\QueryBuilder $target */

        // this will return DQL code
        $dql = $this->execute($target, $operators, $parameters);

        // the root alias can not be determined at compile-time so placeholders are left in the DQL
        $dql = str_replace('@@_ROOT_ALIAS_@@', $target->getRootAliases()[0], $dql);

        // so we apply it to the query builder
        $target->andWhere($dql);

        // now we define the parameters
        foreach ($parameters as $name => $value) {
            $target->setParameter($name, $value);
        }

        // possible improvement: paginate the query to avoid fetching all results at once in memory
        $results = $target->getQuery()->getResult();

        return new FilterResult(count($results), function () use ($results) {
            foreach ($results as $result) {
                yield $result;
            }
        });
    }
}
