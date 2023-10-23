<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

class PaymentValidationResponse
{
    private int $projectId;
    private Order $order;
    private int $status;
    private ?string $payment = null;
    private ?string $country = null;
    private ?string $originalPaymentText = null;
    private ?string $paymentText = null;
    private bool $test = false;
    private ?string $version = null;
    private ?string $requestId = null;
    private ?string $name = null;
    private ?string $sureName = null;
    private ?string $paymentCurrency = null;
    private ?float $paymentAmount = null;
    private ?string $account = null;
    private ?string $type = null;

    public function __construct(int $projectId,  Order $order, int $status)
    {
        $this->projectId = $projectId;
        $this->order = $order;
        $this->status = $status;
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

    public function setPayment(?string $payment): void
    {
        $this->payment = $payment;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getOriginalPaymentText(): ?string
    {
        return $this->originalPaymentText;
    }

    public function setOriginalPaymentText(?string $originalPaymentText): void
    {
        $this->originalPaymentText = $originalPaymentText;
    }

    public function getPaymentText(): ?string
    {
        return $this->paymentText;
    }

    public function setPaymentText(?string $paymentText): void
    {
        $this->paymentText = $paymentText;
    }

    public function isTest(): bool
    {
        return $this->test;
    }

    public function setTest(bool $test): void
    {
        $this->test = $test;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function setRequestId(?string $requestId): void
    {
        $this->requestId = $requestId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSureName(): ?string
    {
        return $this->sureName;
    }

    public function setSureName(?string $sureName): void
    {
        $this->sureName = $sureName;
    }

    public function getPaymentCurrency(): ?string
    {
        return $this->paymentCurrency;
    }

    public function setPaymentCurrency(?string $paymentCurrency): void
    {
        $this->paymentCurrency = $paymentCurrency;
    }

    public function getPaymentAmount(): ?float
    {
        return $this->paymentAmount;
    }

    public function setPaymentAmount(?float $paymentAmount): void
    {
        $this->paymentAmount = $paymentAmount;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(?string $account): void
    {
        $this->account = $account;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
