<?php

namespace RulerZ\Compiler\Target\Elasticsearch;

use Elasticsearch\Client;

class ElasticsearchVisitor extends GenericElasticsearchVisitor
{
    /**
     * {@inheritDoc}
     */
    public function supports($target, $mode)
    {
        return $target instanceof Client;
    }

    /**
     * @inheritDoc
     */
    protected function getExecutorTraits()
    {
        return [
            '\RulerZ\Executor\Elasticsearch\ElasticsearchFilterTrait',
            '\RulerZ\Executor\Polyfill\FilterBasedSatisfaction',
        ];
    }
}
