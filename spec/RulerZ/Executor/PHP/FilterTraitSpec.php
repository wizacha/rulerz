<?php

namespace spec\RulerZ\Executor\PHP;

use PhpSpec\ObjectBehavior;
use RulerZ\Context\ExecutionContext;
use RulerZ\Filter\FilterResult;
use RulerZ\Stub\Executor\ArrayExecutorStub;

class FilterTraitSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('RulerZ\Stub\Executor\ArrayExecutorStub');
    }

    function it_filters_the_target_using_execute()
    {
        $target = [ ['some' => 'item'], ['another' => 'item'] ];

        ArrayExecutorStub::$executeReturn = true;

        $this->filter($target, $parameters = [], $operators = [], new ExecutionContext())
            ->shouldReturnAnInstanceOf(FilterResult::class);
    }
}
