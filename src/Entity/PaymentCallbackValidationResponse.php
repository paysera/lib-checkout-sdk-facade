<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

class PaymentCallbackValidationResponse
{
    private int $projectId;
    private Order $order;
    private int $status;
    private ?string $payment;
    private ?string $originalPaymentText;
    private ?string $paymentText;
    private bool $test;
    private ?string $version;
    private ?string $requestId;
    private ?string $account;
    private ?string $type;

    public function __construct(int $projectId, Order $order, int $status)
    {
        $this->projectId = $projectId;
        $this->order = $order;
        $this->status = $status;
        $this->payment = null;
        $this->originalPaymentText = null;
        $this->paymentText = null;
        $this->test = false;
        $this->version = null;
        $this->requestId = null;
        $this->account = null;
        $this->type = null;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getOriginalPaymentText(): ?string
    {
        return $this->originalPaymentText;
    }

    public function setOriginalPaymentText(?string $originalPaymentText): self
    {
        $this->originalPaymentText = $originalPaymentText;

        return $this;
    }

    public function getPaymentText(): ?string
    {
        return $this->paymentText;
    }

    public function setPaymentText(?string $paymentText): self
    {
        $this->paymentText = $paymentText;

        return $this;
    }

    public function isTest(): bool
    {
        return $this->test;
    }

    public function setTest(?bool $test): self
    {
        $this->test = (bool) $test;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function setRequestId(?string $requestId): self
    {
        $this->requestId = $requestId;

        return $this;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(?string $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
