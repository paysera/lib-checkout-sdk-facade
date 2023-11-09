<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\PaymentMethodCountry;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Entity\Translation;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodCountryTest extends AbstractCase
{
    /**
     * @dataProvider getTitleProvider
     */
    public function testGetTitle(
        array $translations,
        ?string $language,
        string $defaultLanguage,
        $expected,
        string $message
    ): void {
        $paymentMethodCountry = new PaymentMethodCountry('gb', $defaultLanguage);

        foreach ($translations as $translation) {
            $paymentMethodCountry->getTitleTranslations()->append($translation);
        }

        $this->assertEquals($expected, $paymentMethodCountry->getTitle($language), $message);
    }

    public function testIsEmpty(): void
    {
        $paymentMethodCountry = new PaymentMethodCountry('gb', 'en');

        $this->assertTrue(
            $paymentMethodCountry->isEmpty(),
            'Entity without groups must be empty.'
        );

        $paymentMethodCountry->getGroups()->append(m::mock(PaymentMethodGroup::class));
        $this->assertFalse(
            $paymentMethodCountry->isEmpty(),
            'Entity with groups must be not empty.'
        );
    }

    public function testSetDefaultLanguage(): void
    {
        $paymentMethodGroup = m::mock(PaymentMethodGroup::class)
            ->expects()
            ->setDefaultLanguage('it')
            ->once()
            ->getMock();

        $paymentMethodCountry = new PaymentMethodCountry('gb', 'en');
        $paymentMethodCountry->getGroups()->append($paymentMethodGroup);

        $this->assertEquals(
            $paymentMethodCountry,
            $paymentMethodCountry->setDefaultLanguage('it'),
            'Method must return the same instance of entity.'
        );
    }

    public function getTitleProvider(): array
    {
        return [
            'selectedLanguageTranslationFound' => [
                [new Translation('en', 'en text'), new Translation('lt', 'lt text')],
                'lt',
                'en',
                'lt text',
                'Invalid translation if the selected language is present.',
            ],
            'selectedLanguageTranslationAbsentUsedDefaultLanguageTranslation' => [
                [new Translation('en', 'en text'), new Translation('lv', 'lv text')],
                'lt',
                'en',
                'en text',
                'Invalid translation if the selected language is absent, but the default is present.',
            ],
            'selectedLanguageAndDefaultLanguageTranslationsAbsent' => [
                [new Translation('lt', 'lt text'), new Translation('lv', 'lv text')],
                'it',
                'en',
                'gb',
                'Invalid translation if the selected and the default language are absent.',
            ],
            'withoutSelectedLanguage' => [
                [new Translation('lt', 'lt text'), new Translation('lv', 'lv text')],
                null,
                'lv',
                'lv text',
                'Invalid translation if the selected language is null.',
            ],
        ];
    }
}
