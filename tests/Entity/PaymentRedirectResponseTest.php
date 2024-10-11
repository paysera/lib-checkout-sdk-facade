<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Entity;

use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentRedirectResponseTest extends AbstractCase
{
    protected const TEST_URL = 'http://example.paysera.test';
    protected const TEST_DATA = 'qweertrtytyutyu';

    public function testGetRedirectUrl(): void
    {
        $this->assertEquals(
            self::TEST_URL,
            (new PaymentRedirectResponse(self::TEST_URL, self::TEST_DATA))->getRedirectUrl()
        );
    }

    public function testGetData(): void
    {
        $this->assertEquals(
            self::TEST_DATA,
            (new PaymentRedirectResponse(self::TEST_URL, self::TEST_DATA))->getData()
        );
    }
}
