<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

class Order
{
    private int $orderId;
    private string $currency;

    /** Amount in cents the client has to pay. */
    private int $amount;
    private ?string $paymentFirstName;
    private ?string $paymentLastName;
    private ?string $paymentEmail;
    private ?string $paymentStreet;
    private ?string $paymentCity;
    private ?string $paymentState;
    private ?string $paymentZip;
    private ?string $paymentCountryCode;

    public function __construct(int $orderId, int $amount, string $currency)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = strtoupper($currency);
        $this->paymentFirstName = null;
        $this->paymentLastName = null;
        $this->paymentEmail = null;
        $this->paymentStreet = null;
        $this->paymentCity = null;
        $this->paymentState = null;
        $this->paymentZip = null;
        $this->paymentCountryCode = null;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPaymentFirstName(): ?string
    {
        return $this->paymentFirstName;
    }

    public function setPaymentFirstName(?string $paymentFirstName): self
    {
        $this->paymentFirstName = $paymentFirstName;

        return $this;
    }

    public function getPaymentLastName(): ?string
    {
        return $this->paymentLastName;
    }

    public function setPaymentLastName(?string $paymentLastName): self
    {
        $this->paymentLastName = $paymentLastName;

        return $this;
    }

    public function getPaymentEmail(): ?string
    {
        return $this->paymentEmail;
    }

    public function setPaymentEmail(?string $paymentEmail): self
    {
        $this->paymentEmail = $paymentEmail;

        return $this;
    }

    public function getPaymentStreet(): ?string
    {
        return $this->paymentStreet;
    }

    public function setPaymentStreet(?string $paymentStreet): self
    {
        $this->paymentStreet = $paymentStreet;

        return $this;
    }

    public function getPaymentCity(): ?string
    {
        return $this->paymentCity;
    }

    public function setPaymentCity(?string $paymentCity): self
    {
        $this->paymentCity = $paymentCity;

        return $this;
    }

    public function getPaymentState(): ?string
    {
        return $this->paymentState;
    }

    public function setPaymentState(?string $paymentState): self
    {
        $this->paymentState = $paymentState;

        return $this;
    }

    public function getPaymentZip(): ?string
    {
        return $this->paymentZip;
    }

    public function setPaymentZip(?string $paymentZip): self
    {
        $this->paymentZip = $paymentZip;

        return $this;
    }

    public function getPaymentCountryCode(): ?string
    {
        return $this->paymentCountryCode;
    }

    public function setPaymentCountryCode(?string $paymentCountryCode): self
    {
        $this->paymentCountryCode = $paymentCountryCode;

        return $this;
    }
}
