<?php

namespace subzeta\Ruling\Test;

use PHPUnit_Framework_TestCase;
use subzeta\Ruling\Exception\InvalidCallbackException;
use subzeta\Ruling\Exception\InvalidContextException;
use subzeta\Ruling\Exception\InvalidRuleException;
use subzeta\Ruling\Ruling;

class RulingTest extends PHPUnit_Framework_TestCase
{
    const SUCCESS_CALLBACK_STRING = 'Corrientes demolientes';
    const FAIL_CALLBACK_STRING = 'El d�a de los muertos';

    /** @var Ruling */
    private $ruling;

    public function setUp()
    {
        $this->ruling = new Ruling();
    }

    /**
     * @dataProvider invalidContexts
     * @param mixed $context
     * @test
     */
    public function itShouldReturnAnInvalidContextExceptionIfContextIsNotValid($context)
    {
        $this->setExpectedException(
            InvalidContextException::class,
            'Context must be an array with string keys and values.'
        );

        $this->ruling
            ->given($context)
            ->when('blablabla')
            ->then(function(){return null;})
            ->execute();
    }

    /**
     * @dataProvider invalidRules
     * @param $rule
     * @test
     */
    public function itShouldReturnAnInvalidRuleExceptionIfRuleIsNotAStringOrAnArrayOfStrings($rule)
    {
        $this->setExpectedException(
            InvalidRuleException::class,
            'Rule must be a string or an array of strings.'
        );

        $this->ruling
            ->given(['hahaha' => 1])
            ->when($rule)
            ->then(function(){return null;})
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnInvalidRuleExceptionIfRuleIsValid()
    {
        $this->setExpectedExceptionRegExp(
            InvalidRuleException::class,
            '/^(Rules aren\'t semantically valid)(.*)$/'
        );

        $this->ruling
            ->given(['hahaha' => 1])
            ->when('1 < 3 ;')
            ->then(function(){return null;})
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnInvalidCallbackExceptionIfSuccessCallbackIsProvidedButNotCallable()
    {
        $this->setExpectedException(
            InvalidCallbackException::class,
            'Callback must be callable.'
        );

        $this->ruling
            ->given(['heyyo' => 1])
            ->when('1 == 2')
            ->then('morcilla')
            ->execute();
    }

    /**
     * @test
     */
    public function itShouldReturnAnInvalidCallbackExceptionIfFailCallbackIsProvidedAndIsNotCallable()
    {
        $this->setExpectedException(
            InvalidCallbackException::class,
            'Callback must be callable.'
        );

        $this->ruling
            ->given(['heyyo' => true])
            ->when('2 > 3')
            ->then(function(){return null;})
            ->otherwise('morcilla')
            ->execute();
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenRuleAssertsAndSuccessCallbackIsNotSet()
    {
        $this->assertTrue(
            $this->ruling
                ->given(['something' => 10])
                ->when(':something is greater than 5 and :something is less than 15')
                ->execute()
        );
    }

    /**
     * @test
     */
    public function shouldReturnAnStringWhenRuleDoesNotAssertAndFailCallbackIsNotSet()
    {
        $this->assertFalse(
            $this->ruling
                ->given(['something' => 20])
                ->when(':something is greater than 5 and :something is less than 15')
                ->execute()
        );
    }

    /**
     * @test
     */
    public function itShouldReturnACorrectInterpretation()
    {
        $this->assertSame(
            ['(true === false && 1 < 2) || 3 <= 4'],
            $this->ruling
                ->given(['a' => true, 'b' => 2, 'c' => 4])
                ->when('(:a same as false and 1 < :b) or 3 is less or equal to 4')
                ->interpret()
        );
    }

    /**
     * @dataProvider getData
     * @param array $context
     * @param string|string[] $rules
     * @param bool $expectation
     * @test
     */
    public function itShouldReturnTheExpectedBooleanCallback($context, $rules, $expectation)
    {
        $this->assertSame($expectation, $this->ruling->given($context)->when($rules)->execute());
    }

    /**
     * @dataProvider getCallableData
     * @param array $context
     * @param string|string[] $rules
     * @param bool $expectation
     * @test
     */
    public function itShouldReturnTheExpectedCallableCallback($context, $rules, $expectation)
    {
        $this->assertSame(
            $expectation,
            $this->ruling
                ->given($context)
                ->when($rules)
                ->then(function() {
                    return self::SUCCESS_CALLBACK_STRING;
                })
                ->otherwise(function() {
                    return self::FAIL_CALLBACK_STRING;
                })
                ->execute()
        );
    }

    /**
     * @return array
     */
    public function invalidContexts()
    {
        return [
            [1],
            [3.2],
            [function(){return 'This is callable';}],
            [null],
            [''],
            [['']],
            [[':']],
            [['thisIsAValueWithAnIntKey']],
            [[null]],
            [['' => '']],
            [[0 => '']],
            [['' => 0]],
            [[null => '']],
            [['' => null]],
            [['' => null]],
        ];
    }

    /**
     * @return array
     */
    public function invalidRules()
    {
        return [
            [1],
            [3.2],
            [function(){return 'This is callable';}],
            [null],
            [''],
            [[1, 2, 3]],
            [[1.1]],
            [[function(){return 'This is callable';}]],
            [['', '']],
            [[null, '']],
            [['this rule has the rule in the key' => '']],
            [['the first rule is ok but the second it\'s not', 1]],
        ];
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array_merge(
            $this->simpleContextAndSimpleRule(),
            $this->multipleContext(),
            $this->multipleRule(),
            $this->parenthesis(),
            $this->encodings(),
            $this->caseSensitivity(),
            $this->operators(),
            $this->callableContextValue(),
            $this->stricts(),
            $this->notStricts(),
            $this->expectations()
        );
    }

    /**
     * @return array
     */
    public function getCallableData()
    {
        return [
            [
                ['something' => 'fideu�'],
                ':something is equal to "fideu�" and :something isn\'t "croissant"',
                self::FAIL_CALLBACK_STRING
            ],
            [
                ['something' => 'fideu�'],
                ':something is equal to "fideu�" and :something is not equal to "croissant"',
                self::SUCCESS_CALLBACK_STRING
            ],
        ];
    }

    /**
     * @return array
     */
    public function simpleContextAndSimpleRule()
    {
        return [
            [
                ['something' => 10],
                ':something is greater than 5 and :something is less than 15',
                true
            ],
            [
                ['something' => 2.3],
                ':something is greater than 1.5 and :something is less than 3.2',
                true
            ],
            [
                ['something' => 'fideu�'],
                ':something is equal to "fideu�" and :something isn\'t "croissant"',
                false
            ],
            [
                ['something' => 'fideu�'],
                ':something is equal to "fideu�" and :something is not equal to "croissant"',
                true
            ],
        ];
    }

    /**
     * @return array
     */
    public function multipleContext()
    {
        return [
            [
                ['something' => 10, 'somehow' => 'Joe'],
                ':something is greater than 5 and :something is less than 15 and :somehow is equal to "Joe"',
                true
            ],
        ];
    }

    /**
     * @return array
     */
    public function multipleRule()
    {
        return [
            [
                ['something' => 'fricand�'],
                [':something is equal to "fricand�"', ':something is not equal to "fideu�"'],
                true
            ],
            [
                ['something' => 'fricand�'],
                [':something is not equal to "fricand�"', ':something is equal to "fideu�"'],
                false
            ],
            [
                ['something' => 3],
                [':something is equal to 3', ':something is equal to 4'],
                false
            ],
            [
                ['something' => 'fricand�'],
                [':something is not equal to "fricand�"', ':something is equal to "fricand�"'],
                false
            ],
            [
                ['something' => 8],
                [':something is less or equal to 10', ':something is greater than 6'],
                true
            ],
        ];
    }

    /**
     * @return array
     */
    public function parenthesis()
    {
        return [
            [
                ['something' => 'fideu�'],
                '(:something is equal to "fideu�" and :something is not equal to "croissant") or :something is equal to "fideu�"',
                true
            ],
            [
                ['something' => 'tortilla de patatas'],
                '(:something is equal to "tortilla de patatas" and :something is equal to "antananaribo") or :something is equal to "madalenas"',
                false
            ],
        ];
    }

    /**
     * @return array
     */
    public function encodings()
    {
        return [
            [
                ['something' => 'fideu�'],
                ':something is equal to "fideu�" and :something is not equal to "croissant"',
                false
            ],
        ];
    }

    /**
     * @return array
     */
    public function caseSensitivity()
    {
        return [
            [
                ['something' => 'fideua'],
                ':something is equal to "FIDEUA" and :something is not equal to "croissant"',
                false
            ]
        ];
    }

    /**
     * @return array
     */
    public function operators()
    {
        return [
            [
                ['something' => 'gazpacho'],
                ':something is "gazpacho" and :something is not "salmorejo"',
                true
            ],
            [
                ['something' => ['a', 'b', 'c']],
                '\'a\' in :something',
                true
            ],
            [
                ['something' => ['1', '2', '3']],
                '\'1\' in :something',
                true
            ],
            [
                ['something' => ['1', '2', '3']],
                '1 in :something',
                false
            ],
            [
                ['something' => [1, 2, 3]],
                '\'1\' in :something',
                false
            ],
            [
                ['something' => [1, 2, 3]],
                '"1" in :something',
                false
            ],
            [
                ['something' => [1, 2, 3]],
                '1 in :something',
                true
            ],
            [
                ['something' => [1.13, 2, 3]],
                '1.13 in :something',
                true
            ]
        ];
    }

    /**
     * @return array
     */
    public function callableContextValue()
    {
        return [
            [
                ['purchase' => function(){return 'gazpacho';}, 'price' => function(){return 40;}],
                ':purchase is "gazpacho" and :price is greater than 50',
                false
            ],
            [
                ['logged' => function(){return true;}, 'name' => function(){return 'foo';}],
                ':logged is true and :name is "foo"',
                true
            ],
        ];
    }

    public function stricts()
    {
        return [
            [
                ['pretty' => 1, 'likes_acdc' => function(){return true;}],
                ':pretty same as 1 and :likes_acdc is true',
                true
            ],
            [
                ['pretty' => false, 'likes_acdc' => function(){return true;}],
                ':pretty not same as true or :likes_acdc same as true',
                true
            ],
            [
                ['pretty' => true, 'likes_acdc' => function(){return false;}],
                ':pretty same as true and :likes_acdc same as false',
                true
            ],
            [
                ['pretty' => '1'],
                ':pretty same as "1"',
                true
            ],
            [
                ['pretty' => 1],
                ':pretty same as 1',
                true
            ],
        ];
    }

    public function notStricts()
    {
        return [
            [
                ['pretty' => 1, 'likes_acdc' => function(){return true;}],
                ':pretty is true and :likes_acdc is not true',
                false
            ],
            [
                ['logged' => function(){return 'true';}, 'name' => function(){return 'foo';}],
                ':logged is "true" and :name is "foo"',
                true
            ],
            [
                ['logged' => function(){return 1;}, 'name' => function(){return 'foo';}],
                ':logged is true and :name is "foo"',
                true
            ],
            [
                ['pretty' => 0, 'likes_acdc' => function(){return true;}],
                ':pretty is not true and :likes_acdc is not true',
                false
            ],
            [
                ['logged' => function(){return 'false';}, 'name' => function(){return 'foo';}],
                ':logged isn\'t "true" and :name is "foo"',
                true
            ],
        ];
    }

    public function expectations()
    {
        return [
            [
                ['something' => null],
                ':something is null',
                true
            ],
            [
                ['something' => 'null'],
                ':something is not null',
                true
            ],
            [
                ['something' => true],
                ':something is true',
                true
            ],
            [
                ['something' => false],
                ':something is false',
                true
            ],
            [
                ['something' => 'true'],
                ':something is "true"',
                true
            ],
            [
                ['something' => 'false'],
                ':something is "false"',
                true
            ],
            [
                ['something' => 1],
                ':something is true',
                true
            ],
            [
                ['something' => 0],
                ':something is false',
                true
            ],
            [
                ['something' => 1.1],
                ':something is less than 1.2',
                true
            ],
            [
                ['something' => 1.3],
                ':something is greater or equal to 1.2',
                true
            ],
            [
                ['something' => 'The Rolling Stones'],
                ':something is "The Rolling Stones"',
                true
            ],
            [
                ['something' => 'The Rolling Stones'],
                ':something is \'The Rolling Stones\'',
                true
            ],
            [
                ['something' => 'The Rolling Stones'],
                ":something is 'The Rolling Stones'",
                true
            ],
            [
                ['something' => 'The Cardigans'],
                ':something is not "The Cure"',
                true
            ],
            [
                ['something' => 'The Cardigans'],
                ':something is not \'The Cure\'',
                true
            ],
            [
                ['something' => 'The Cardigans'],
                ":something is not 'The Cure'",
                true
            ],
            [
                ['something' => 'Pink Floyd'],
                ':something isn"t "Deep Purple"',
                true
            ],
            [
                ['something' => 'Pink Floyd'],
                ':something isn\'t \'Deep Purple\'',
                true
            ],
            [
                ['something' => 'Pink Floyd'],
                ":something isn't 'Deep Purple'",
                true
            ],
            [
                ['number' => 'Pink Floyd'],
                ":number in ['Deep Purple','Pink Floyd']",
                true
            ],
            [
                ['number' => 3],
                ":number contained in [1,2,3,4]",
                true
            ],
            [
                ['number' => '34'],
                ":number contained in [1,2,'34',3,4]",
                true
            ],
            [
                ['numbers' => [1, 2, 3]],
                "3 in :numbers",
                true
            ],
            [
                ['strings' => ['the', 'rolling', 'stones']],
                "'rolling' in :strings",
                true
            ],
            [
                ['strings' => ['the', 'rolling', 'stones']],
                "'potato' in :strings",
                false
            ]
        ];
    }
}