<?php

namespace spec\RulerZ;

use Elasticsearch\Client;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use RulerZ\Compiler\Compiler;
use RulerZ\Compiler\EvalCompiler;
use RulerZ\Compiler\FileCompiler;
use RulerZ\Compiler\Target\CompilationTarget;
use RulerZ\Compiler\Target\Elasticsearch\ElasticsearchVisitor;
use RulerZ\Executor\Executor;
use RulerZ\Filter\FilterResult;
use RulerZ\Parser\HoaParser;
use RulerZ\Spec\Specification;

class RulerZSpec extends ObjectBehavior
{
    function let(Compiler $compiler, CompilationTarget $compilationTarget)
    {
        $this->beConstructedWith($compiler, [$compilationTarget]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('RulerZ\RulerZ');
    }

    function it_accepts_new_executors_after_construction(CompilationTarget $anotherCompilationTarget)
    {
        $this->registerCompilationTarget($anotherCompilationTarget);
    }

    function it_chooses_the_right_compilation_target_for_a_given_target(Compiler $compiler, CompilationTarget $compilationTargetYes, CompilationTarget $compilationTargetNo, Executor $executor)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];

        $compiler->compile($rule, $compilationTargetYes)->willReturn($executor);

        $compilationTargetYes->supports($target, CompilationTarget::MODE_FILTER)->willReturn(true);
        $compilationTargetYes->getOperators()->willReturn($operators);

        $compilationTargetNo->supports($target, CompilationTarget::MODE_FILTER)->willReturn(false);

        $executor->filter($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->shouldBeCalled();

        $this->beConstructedWith($compiler, [$compilationTargetNo, $compilationTargetYes]);

        $this->filter($target, $rule);
    }

    function it_can_filter_a_target_with_a_rule(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];
        $result    = 'dummy result';

        $compiler->compile($rule, $compilationTarget)->willReturn($executor);

        $compilationTarget->supports($target, CompilationTarget::MODE_FILTER)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->filter($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->filter($target, $rule)->shouldReturn($result);
    }

    function it_can_filter_a_target_with_a_specification(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor, Specification $spec)
    {
        $target     = ['dummy target'];
        $rule       = 'dummy rule';
        $operators  = ['dummy operator'];
        $result     = 'dummy result';
        $parameters = ['dummy param'];

        $spec->getRule()->willReturn($rule);
        $spec->getParameters()->willReturn($parameters);

        $compiler->compile($rule, $compilationTarget)->willReturn($executor);

        $compilationTarget->supports($target, CompilationTarget::MODE_FILTER)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->filter($target, $parameters, $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->filterSpec($target, $spec)->shouldReturn($result);
    }

    function it_can_check_if_a_target_satisfies_a_rule(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor)
    {
        $target    = ['dummy target'];
        $rule      = 'dummy rule';
        $operators = ['dummy operator'];
        $result    = true;

        $compiler->compile($rule, $compilationTarget)->willReturn($executor);

        $compilationTarget->supports($target, CompilationTarget::MODE_SATISFIES)->willReturn(true);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->satisfies($target, [], $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->satisfies($target, $rule)->shouldReturn($result);
    }

    function it_can_check_if_a_target_satisfies_a_specification(Compiler $compiler, CompilationTarget $compilationTarget, Executor $executor, Specification $spec)
    {
        $target     = ['dummy target'];
        $rule       = 'dummy rule';
        $operators  = ['dummy operator'];
        $parameters = ['dummy param'];
        $result    = true;

        $spec->getRule()->willReturn($rule);
        $spec->getParameters()->willReturn($parameters);

        $compiler->compile($rule, $compilationTarget)->willReturn($executor);

        $compilationTarget->supports($target, CompilationTarget::MODE_SATISFIES)->willReturn($result);
        $compilationTarget->getOperators()->willReturn($operators);

        $executor->satisfies($target, $parameters, $operators, Argument::type('\RulerZ\Context\ExecutionContext'))->willReturn($result);

        $this->satisfiesSpec($target, $spec)->shouldReturn(true);
    }

    function it_cant_filter_without_a_compilation_target()
    {
        $this
            ->shouldThrow('RulerZ\Exception\TargetUnsupportedException')
            ->duringFilter(['some target'], 'points > 30');
    }

    function it_can_filter_using_elasticsearch()
    {
        $this->beConstructedWith(new EvalCompiler(new HoaParser), [new ElasticsearchVisitor]);

        $index = 'foo';
        $type = 'bar';

        $elasticsearch = new Client();
        // Clear existing records
        $elasticsearch->indices()->deleteMapping([
            'index' => $index,
            'type' => $type,
        ]);
        // Add some test records
        $elasticsearch->index([
            'index' => $index,
            'type' => $type,
            'id' => '123',
            'body' => [
                'price' => 123,
                'categoryId' => 1,
            ],
        ]);
        $elasticsearch->index([
            'index' => $index,
            'type' => $type,
            'id' => '456',
            'body' => [
                'price' => 456,
                'categoryId' => 2,
            ],
        ]);
        $elasticsearch->index([
            'index' => $index,
            'type' => $type,
            'id' => '789',
            'body' => [
                'price' => 789,
                'categoryId' => 3,
            ],
        ]);
        // Avoid race conditions (Elasticsearch is not synchronous)
        $elasticsearch->indices()->refresh();

        $rule = 'price < :maxPrice and categoryId in :categoryIds';
        $parameters = [
            'maxPrice' => 600,
            'categoryIds' => [2, 4, 7],
        ];
        $context = [
            'index' => $index,
            'type' => $type,
        ];

        $this->filter($elasticsearch, $rule, $parameters, $context)->shouldHaveResults([
            [
                'price' => 456,
                'categoryId' => 2,
            ]
        ]);
    }

    public function getMatchers()
    {
        return [
            'haveResults' => function ($subject, $expectedResults) {
                if (!$subject instanceof FilterResult) {
                    throw new FailureException('The method did not return a FilterResult object');
                }
                if ($subject->getCount() !== count($expectedResults)) {
                    throw new FailureException(sprintf(
                        'Expected %d result, got %d',
                        count($expectedResults),
                        $subject->getCount()
                    ));
                }
                $i = 0;
                foreach ($subject->getGenerator() as $result) {
                    $expectedResult = $expectedResults[$i];
                    if ($result !== $expectedResult) {
                        throw new FailureException('Wrong result');
                    }
                }
                return true;
            }
        ];
    }
}
