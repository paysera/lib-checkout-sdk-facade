<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\Request\PaymentRedirectRequest;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentRedirectRequestNormalizer;
use Paysera\CheckoutSdk\Tests\AbstractCase;
use WebToPay;

class PaymentRedirectRequestNormalizerTest extends AbstractCase
{
    protected ?PaymentRedirectRequestNormalizer $normalizer = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->normalizer = $this->container->get(PaymentRedirectRequestNormalizer::class);
    }

    public function testNormalize(): void
    {
        $data = $this->normalizer->normalize($this->getRequest());

        $this->assertEquals(
            [
                'orderid' => 10,
                'amount' => 100,
                'currency' => 'EUR',
                'accepturl' => 'acceptUrl',
                'cancelurl' => 'cancelUrl',
                'callbackurl' => 'callbackUrl',
                'version' => '1',
                'lang' => 'language',
                'payment' => 'payment',
                'country' => 'country',
                'paytext' => 'payment text',
                'p_firstname' => 'John',
                'p_lastname' => 'Doe',
                'p_email' => 'john.doe@paysera.net',
                'p_street' => 'street',
                'p_city' => 'city',
                'p_zip' => 'zip',
                'p_state' => 'state',
                'p_countrycode' => 'country',
                'test' => 1,
                'buyer_consent' => 1,
                'time_limit' => 'limit',
                'php_version' => phpversion(),
                'plugin_name' => 'plugin',
                'plugin_version' => '1',
                'cms_version' => '2',
            ],
            $data
        );
    }

    public function testNormalizeWithDefaultVersion(): void
    {
        $request = $this->getRequest()
            ->setVersion(null);

        $data = $this->normalizer->normalize($request);

        $this->assertEquals(WebToPay::VERSION, $data['version']);
    }

    public function testNormalizeWithEmptyFields(): void
    {
        $request = $this->getRequest()
            ->setPaymentText(null)
            ->setCountry(null)
            ->setPluginName(null)
            ->setPluginVersion(null)
            ->setCmsVersion(null);

        $data = $this->normalizer->normalize($request);

        $this->assertArrayNotHasKey('paytext', $data);
        $this->assertArrayNotHasKey('country', $data);
        $this->assertArrayNotHasKey('plugin_name', $data);
        $this->assertArrayNotHasKey('plugin_version', $data);
        $this->assertArrayNotHasKey('cms_version', $data);
    }

    protected function getRequest(): PaymentRedirectRequest
    {
        $order = new Order(10, 100, 'EUR');
        $order->setPayerFirstName('John')
            ->setPayerLastName('Doe')
            ->setPayerEmail('john.doe@paysera.net')
            ->setPayerStreet('street')
            ->setPayerCity('city')
            ->setPayerZip('zip')
            ->setPayerState('state')
            ->setPayerCountryCode('country');

        $request = new PaymentRedirectRequest(
            1,
            'password',
            'acceptUrl',
            'cancelUrl',
            'callbackUrl',
            $order
        );
        $request->setVersion('1')
            ->setLanguage('language')
            ->setPayment('payment')
            ->setCountry('country')
            ->setPaymentText('payment text')
            ->setTest(true)
            ->setBuyerConsent(true)
            ->setTimeLimit('limit')
            ->setPluginName('plugin')
            ->setPluginVersion('1')
            ->setCmsVersion('2');

        return $request;
    }
}
