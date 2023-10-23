<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity;

use Mockery as m;
use Paysera\CheckoutSdk\Entity\PaymentMethod;
use Paysera\CheckoutSdk\Entity\PaymentMethodGroup;
use Paysera\CheckoutSdk\Entity\Translation;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodGroupTest extends AbstractCase
{
    /**
     * @dataProvider getTitleProvider
     */
    public function testGetTitle(array $translations, string $language, $expected, string $message): void
    {
        $paymentMethodGroup = new PaymentMethodGroup('key', 'en');

        foreach ($translations as $translation) {
            $paymentMethodGroup->getTitleTranslations()->append($translation);
        }

        $this->assertEquals($expected, $paymentMethodGroup->getTitle($language), $message);
    }

    public function testIsEmpty(): void
    {
        $paymentMethodGroup = new PaymentMethodGroup('key', 'en');

        $this->assertTrue(
            $paymentMethodGroup->isEmpty(),
            'Entity without payment methods must be empty.'
        );

        $paymentMethodGroup->getPaymentMethods()->append(m::mock(PaymentMethod::class));
        $this->assertFalse(
            $paymentMethodGroup->isEmpty(),
            'Entity with payment methods must be not empty.'
        );
    }

    public function testSetDefaultLanguage(): void
    {
        $paymentMethod = m::mock(PaymentMethod::class);
        $paymentMethod->expects()
            ->setDefaultLanguage('it')
            ->once();

        $paymentMethodGroup = new PaymentMethodGroup('key', 'en');
        $paymentMethodGroup->getPaymentMethods()->append($paymentMethod);

        $this->assertEquals(
            $paymentMethodGroup,
            $paymentMethodGroup->setDefaultLanguage('it'),
            'Method must return the same instance of entity.'
        );
    }

    public function getTitleProvider(): array
    {
        return [
            'selectedLanguageTranslationFound' => [
                [new Translation('en', 'en text'), new Translation('lt', 'lt text')],
                'lt',
                'lt text',
                'Invalid translation if the selected language is present.',
            ],
            'selectedLanguageTranslationAbsentUsedDefaultLanguageTranslation' => [
                [new Translation('en', 'en text'), new Translation('lv', 'lv text')],
                'lt',
                'en text',
                'Invalid translation if the selected language is absent, but the default is present.',
            ],
            'selectedLanguageAndDefaultLanguageTranslationsAbsent' => [
                [new Translation('lt', 'lt text'), new Translation('lv', 'lv text')],
                'it',
                'key',
                'Invalid translation if the selected and the default language are absent.',
            ],
        ];
    }
}
