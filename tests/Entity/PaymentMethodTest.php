<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity;

use Paysera\CheckoutSdk\Entity\PaymentMethod;
use Paysera\CheckoutSdk\Entity\Translation;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentMethodTest extends AbstractCase
{
    /**
     * @dataProvider getLogoUrlProvider
     */
    public function testGetLogoUrl(array $translations, string $language, $expected, string $message): void
    {
        $paymentMethod = new PaymentMethod('key', 'en');

        foreach ($translations as $translation) {
            $paymentMethod->getLogos()->append($translation);
        }

        $this->assertEquals($expected, $paymentMethod->getLogoUrl($language), $message);
    }

    /**
     * @dataProvider getTitleProvider
     */
    public function testGetTitle(array $translations, string $language, $expected, string $message): void
    {
        $paymentMethod = new PaymentMethod('key', 'en');

        foreach ($translations as $translation) {
            $paymentMethod->getTitleTranslations()->append($translation);
        }

        $this->assertEquals($expected, $paymentMethod->getTitle($language), $message);
    }

    public function getLogoUrlProvider(): array
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
                null,
                'Invalid translation if the selected and the default language are absent.',
            ],
        ];
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
