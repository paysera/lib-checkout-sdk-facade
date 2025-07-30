<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

/**
 * This entity provides the necessary integration fields for refund data.
 * @link https://developers.paysera.com/en/checkout/integrations/integration-specification
 */
class Refund
{
    private ?string $refundAmount;
    private ?string $refundCurrency;
    private ?string $refundCommissionAmount;
    private ?string $refundCommissionCurrency;
    private ?int $refundTimestamp;

    public function __construct(
        ?string $refundAmount = null,
        ?string $refundCurrency = null,
        ?string $refundCommissionAmount = null,
        ?string $refundCommissionCurrency = null,
        ?int $refundTimestamp = null
    ) {
        $this->refundAmount = $refundAmount;
        $this->refundCurrency = $refundCurrency;
        $this->refundCommissionAmount = $refundCommissionAmount;
        $this->refundCommissionCurrency = $refundCommissionCurrency;
        $this->refundTimestamp = $refundTimestamp;
    }

    public function getRefundAmount(): ?string
    {
        return $this->refundAmount;
    }

    public function setRefundAmount(?string $refundAmount): self
    {
        $this->refundAmount = $refundAmount;
        return $this;
    }

    public function getRefundCurrency(): ?string
    {
        return $this->refundCurrency;
    }

    public function setRefundCurrency(?string $refundCurrency): self
    {
        $this->refundCurrency = $refundCurrency;
        return $this;
    }

    public function getRefundCommissionAmount(): ?string
    {
        return $this->refundCommissionAmount;
    }

    public function setRefundCommissionAmount(?string $refundCommissionAmount): self
    {
        $this->refundCommissionAmount = $refundCommissionAmount;
        return $this;
    }

    public function getRefundCommissionCurrency(): ?string
    {
        return $this->refundCommissionCurrency;
    }

    public function setRefundCommissionCurrency(?string $refundCommissionCurrency): self
    {
        $this->refundCommissionCurrency = $refundCommissionCurrency;
        return $this;
    }

    public function getRefundTimestamp(): ?int
    {
        return $this->refundTimestamp;
    }

    public function setRefundTimestamp(?int $refundTimestamp): self
    {
        $this->refundTimestamp = $refundTimestamp;
        return $this;
    }
}
