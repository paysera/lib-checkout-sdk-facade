<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Validator;

use Paysera\CheckoutSdk\Exception\CheckoutIntegrationException;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use Paysera\CheckoutSdk\Validator\CountryCodeIso2Validator;

class CountryCodeIso2ValidatorTest extends AbstractCase
{
    /**
     * @dataProvider countriesProvider
     */
    public function testValidate(string $countryCode, bool $isValid): void
    {
        $validator =  new CountryCodeIso2Validator();

        if ($isValid === false) {
            $this->expectException(CheckoutIntegrationException::class);
            $this->expectExceptionCode(CheckoutIntegrationException::E_VALIDATION);
        } else {
            $this->expectNotToPerformAssertions();
        }

        $validator->validate($countryCode);
    }

    public function countriesProvider(): array
    {
        return [
            'testLowercase'         => ['gb', true],
            'testCapitalized'       => ['LT', true],
            'testFromCapitalLetter' => ['Lv', true],
            'testEmptyString'       => ['', false],
            'testThreeLetters'      => ['USA', false],
            'testUndefined'         => ['test', false],
        ];
    }
}
