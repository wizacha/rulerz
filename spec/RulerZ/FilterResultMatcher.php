<?php

namespace spec\RulerZ;

use PhpSpec\Exception\Example\FailureException;
use RulerZ\Filter\FilterResult;

trait FilterResultMatcher
{
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
                $results = $subject->getResults();
                if (!$results instanceof \Generator) {
                    throw new FailureException('The getGenerator() did not return a generator');
                }
                $results = iterator_to_array($results);
                foreach ($results as $i => $result) {
                    $expectedResult = $expectedResults[$i];
                    if ($result !== $expectedResult) {
                        throw new FailureException(sprintf(
                            "Wrong result %d:\nExpected:\n%s\nActual:\n%s",
                            $i,
                            var_export($expectedResults, true),
                            var_export($results, true)
                        ));
                    }
                }
                return true;
            }
        ];
    }
}
