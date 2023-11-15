<?php

namespace Paysera\CheckoutSdk\Tests\Service;

use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;
use Paysera\CheckoutSdk\Entity\Translation;
use Paysera\CheckoutSdk\Service\Translator;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class TranslatorTest extends AbstractCase
{
    /**
     * @dataProvider getTranslate
     */
    public function testTranslate(
        TranslationCollection $translations,
        string $language,
        $expected,
        string $message
    ): void {
        $translator = new Translator();

        $this->assertEquals($expected, $translator->translate($translations, $language), $message);
    }

    public function getTranslate(): array
    {
        return [
            'selectedLanguageFound' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation('lt', 'lt text')
                ]),
                'lt',
                'lt text',
                'Invalid return value if the selected language is present.',
            ],
            'selectedLanguageAbsent' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation('lt', 'lt text')
                ]),
                'it',
                null,
                'Invalid return value if the selected language is absent.',
            ],
        ];
    }
}
