<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Tests\Provider\WebToPay\Adapter;

use Paysera\CheckoutSdk\Entity\Request\PaymentCallbackValidationRequest;
use Paysera\CheckoutSdk\Provider\WebToPay\Adapter\PaymentCallbackValidationRequestNormalizer;
use Paysera\CheckoutSdk\Tests\AbstractCase;

class PaymentCallbackValidationRequestNormalizerTest extends AbstractCase
{
    protected ?PaymentCallbackValidationRequestNormalizer $normalizer = null;

    public function mockeryTestSetUp(): void
    {
        parent::mockeryTestSetUp();

        $this->normalizer = $this->container->get(PaymentCallbackValidationRequestNormalizer::class);
    }

    public function testNormalize(): void
    {
        $data = $this->normalizer->normalize($this->getRequest());

        $this->assertEquals(
            [
                'data' => 'data',
                'ss1' => 'ss1',
                'ss2' => 'ss2',
            ],
            $data
        );
    }

    public function testNormalizeWithEmptyFields(): void
    {
        $request = $this->getRequest()
            ->setSs2(null);

        $data = $this->normalizer->normalize($request);

        $this->assertEquals(
            [
                'data' => 'data',
                'ss1' => 'ss1',
            ],
            $data
        );
    }

    protected function getRequest(): PaymentCallbackValidationRequest
    {
        $request = new PaymentCallbackValidationRequest(1, 'password', 'data');

        return $request->setSs1('ss1')
            ->setSs2('ss2');
    }
}
