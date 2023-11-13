<?php

namespace Paysera\CheckoutSdk\Tests\Entity;

use Paysera\CheckoutSdk\Entity\PaymentRedirectResponse;
use Paysera\CheckoutSdk\Tests\AbstractCase;

/**
* @runTestsInSeparateProcesses
* @preserveGlobalState disabled
*/
class PaymentRedirectResponseTest extends AbstractCase
{
    protected const TEST_URL = 'http://example.paysera.test';

    protected ?PaymentRedirectResponse $paymentRedirectResponse = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->paymentRedirectResponse = new PaymentRedirectResponse(self::TEST_URL);
    }

    public function testGetRedirectUrl(): void
    {
        $this->assertEquals(self::TEST_URL, $this->paymentRedirectResponse->getRedirectUrl());
    }

    public function testRedirectOutput(): void
    {
        $this->assertIsString($this->paymentRedirectResponse->getRedirectOutput());
        $this->assertNotEmpty($this->paymentRedirectResponse->getRedirectOutput());

        $this->paymentRedirectResponse->setRedirectOutput('test');

        $this->assertEquals('test', $this->paymentRedirectResponse->getRedirectOutput());
    }
}
