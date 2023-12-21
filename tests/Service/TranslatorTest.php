<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Service;

use Paysera\CheckoutSdk\Entity\Collection\TranslationCollection;
use Paysera\CheckoutSdk\Entity\Translation;
use Paysera\CheckoutSdk\Service\TranslatableLogoInterface;
use Paysera\CheckoutSdk\Service\TranslatableTitleInterface;
use Paysera\CheckoutSdk\Service\Translator;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class TranslatorTest extends AbstractCase
{
    protected ?Translator $translator = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->translator = $this->container->get(Translator::class);
    }

    /**
     * @dataProvider getTitleDataProvider
     */
    public function testGetTitle(
        TranslationCollection $translations,
        string $language,
        $expected,
        string $message
    ): void {
        $entityMock = $this->createMock(TranslatableTitleInterface::class);
        $entityMock->method('getTitleTranslations')
            ->willReturn($translations);
        $entityMock->method('getFallbackTitle')
            ->willReturn('fallback');

        $this->assertEquals($expected, $this->translator->getTitle($entityMock, $language), $message);
    }

    public function getTitleDataProvider(): array
    {
        return [
            'selectedTitleFound' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation(Translator::DEFAULT_LANGUAGE, 'default text'),
                ]),
                'en',
                'en text',
                'Invalid return value if the selected language title is present.',
            ],
            'defaultTitleFound' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation(Translator::DEFAULT_LANGUAGE, 'default text'),
                ]),
                'it',
                'default text',
                'Invalid return value if the default language title is present.',
            ],
            'fallbackTitle' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                ]),
                'it',
                'fallback',
                'Invalid return value for fallback title.',
            ],
        ];
    }

    /**
     * @dataProvider getLogoDataProvider
     */
    public function testGetLogo(
        TranslationCollection $translations,
        string $language,
        $expected,
        string $message
    ): void {
        $entityMock = $this->createMock(TranslatableLogoInterface::class);
        $entityMock->method('getLogos')
            ->willReturn($translations);

        $this->assertEquals($expected, $this->translator->getLogo($entityMock, $language), $message);
    }

    public function getLogoDataProvider(): array
    {
        return [
            'selectedLogoFound' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation(Translator::DEFAULT_LANGUAGE, 'default text'),
                ]),
                'en',
                'en text',
                'Invalid return value if the selected language logo is present.',
            ],
            'defaultLanguageFound' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation(Translator::DEFAULT_LANGUAGE, 'default text'),
                ]),
                'it',
                'default text',
                'Invalid return value if the default language logo is present.',
            ],
            'absentLogo' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                ]),
                'it',
                null,
                'Invalid return value for absent logo.',
            ],
        ];
    }

    /**
     * @dataProvider getTranslateDataProvider
     */
    public function testTranslate(
        TranslationCollection $translations,
        string $language,
        $expected,
        string $message
    ): void {
        $this->assertEquals($expected, $this->translator->translate($translations, $language), $message);
    }

    public function getTranslateDataProvider(): array
    {
        return [
            'selectedLanguageFound' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation('lt', 'lt text'),
                ]),
                'lt',
                'lt text',
                'Invalid return value if the selected language is present.',
            ],
            'selectedLanguageAbsent' => [
                new TranslationCollection([
                    new Translation('en', 'en text'),
                    new Translation('lt', 'lt text'),
                ]),
                'it',
                null,
                'Invalid return value if the selected language is absent.',
            ],
        ];
    }
}
