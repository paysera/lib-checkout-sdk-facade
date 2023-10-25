<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Util;

use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Util\Invader;
use Paysera\CheckoutSdk\Util\TypeConverter;

class TypeConverterTest extends AbstractCase
{
    protected ?TypeConverter $typeConverter = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->typeConverter = new TypeConverter();
    }

    /**
     * @dataProvider defaultConvertProvider
     * @dataProvider integerConvertProvider
     * @dataProvider floatConvertProvider
     * @dataProvider booleanConvertProvider
     */
    public function testConvert($value, int $type, $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->typeConverter->convert($value, $type),
            'Returned value must be equal to expected.'
        );

    }

    public function defaultConvertProvider(): array
    {
        return [
            'Return integer as it is'             => [1, TypeConverter::DEFAULT, 1],
            'Return float as it is'               => [1.11, TypeConverter::DEFAULT, 1.11],
            'Return string with integer as it is' => ['1', TypeConverter::DEFAULT, '1'],
            'Return string with float as it is'   => ['1.11', TypeConverter::DEFAULT, '1.11'],
            'Return empty string as it is'        => ['', TypeConverter::DEFAULT, ''],
            'Return string with text as it is'    => ['test Test', TypeConverter::DEFAULT, 'test Test'],
        ];
    }

    public function integerConvertProvider(): array
    {
        return [
            'Cast float to integer'                      => [1.11, TypeConverter::INT, 1],
            'Cast string with integers to integer'       => ['123', TypeConverter::INT, 123],
            'Cast string with float to integer'          => ['123.11', TypeConverter::INT, 123],
            'Cast empty string to integer'               => ['', TypeConverter::INT, 0],
            'Cast string with text to integer'           => ['test Test', TypeConverter::INT, 0],
            'Cast string with integer prefix to integer' => ['11a222Bc3333', TypeConverter::INT, 11],
            'Cast boolean true to integer'               => [true, TypeConverter::INT, 1],
            'Cast boolean false to integer'              => [false, TypeConverter::INT, 0],
            'Cast null to integer'                       => [null, TypeConverter::INT, 0],
        ];
    }

    public function floatConvertProvider(): array
    {
        return [
            'Cast integer to float'                    => [1, TypeConverter::FLOAT, 1.0],
            'Cast string with integers to float'       => ['123', TypeConverter::FLOAT, 123.0],
            'Cast string with float to float'          => ['123.11', TypeConverter::FLOAT, 123.11],
            'Cast empty string to float'               => ['', TypeConverter::FLOAT, 0.0],
            'Cast string with text to float'           => ['test Test', TypeConverter::FLOAT, 0.0],
            'Cast string with integer prefix to float' => ['11a222Bc3333', TypeConverter::FLOAT, 11.0],
            'Cast string with float prefix to float'   => ['11.22a222Bc3333', TypeConverter::FLOAT, 11.22],
            'Cast boolean true to float'               => [true, TypeConverter::FLOAT, 1.0],
            'Cast boolean false to float'              => [false, TypeConverter::FLOAT, 0.0],
            'Cast null to float'                       => [null, TypeConverter::FLOAT, 0.0],
        ];
    }

    public function booleanConvertProvider(): array
    {
        return [
            'Cast positive integer to boolean'  => [1, TypeConverter::BOOL, true],
            'Cast negative integer to boolean'  => [-1, TypeConverter::BOOL, true],
            'Cast zero integer to boolean'      => [0, TypeConverter::BOOL, false],
            'Cast positive float to boolean'    => [1.11, TypeConverter::BOOL, true],
            'Cast negative float to boolean'    => [-0.11, TypeConverter::BOOL, true],
            'Cast zero float to boolean'        => [0.0, TypeConverter::BOOL, false],
            'Cast string with 1 to boolean'     => ['1', TypeConverter::BOOL, true],
            'Cast string with 2 to boolean'     => ['2', TypeConverter::BOOL, false],
            'Cast string with 0 to boolean'     => ['0', TypeConverter::BOOL, false],
            'Cast string with true to boolean'  => ['true', TypeConverter::BOOL, true],
            'Cast string with false to boolean' => ['false', TypeConverter::BOOL, false],
            'Cast empty string to boolean'      => ['', TypeConverter::BOOL, false],
            'Cast string with null to boolean'  => ['null', TypeConverter::BOOL, false],
            'Cast null to boolean'              => [null, TypeConverter::BOOL, false],
        ];
    }
}
