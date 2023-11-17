<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Request;

use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\RequestInterface;

class PaymentRedirectRequest implements RequestInterface
{
    private int $projectId;
    private string $projectPassword;
    private string $acceptUrl;
    private string $cancelUrl;
    private string $callbackUrl;
    private ?string $payment;
    private ?string $language;
    private ?string $paymentText;
    private bool $test;
    private ?string $timeLimit;
    private Order $order;

    public function __construct(
        int $projectId,
        string $projectPassword,
        string $acceptUrl,
        string $cancelUrl,
        string $callbackUrl,
        Order $order
    ) {
        $this->projectId = $projectId;
        $this->projectPassword = $projectPassword;
        $this->acceptUrl = $acceptUrl;
        $this->cancelUrl = $cancelUrl;
        $this->callbackUrl = $callbackUrl;
        $this->order = $order;
        $this->payment = null;
        $this->language = null;
        $this->paymentText = null;
        $this->test = false;
        $this->timeLimit = null;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getProjectPassword(): string
    {
        return $this->projectPassword;
    }

    public function getAcceptUrl(): string
    {
        return $this->acceptUrl;
    }

    public function getCancelUrl(): string
    {
        return $this->cancelUrl;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;

        if (is_string($payment)) {
            $this->payment = strtolower($payment);
        }

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

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

    public function getTest(): bool
    {
        return $this->test;
    }

    public function setTest(?bool $test): self
    {
        $this->test = $test ?? false;

        return $this;
    }

    public function getTimeLimit(): ?string
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(?string $timeLimit): self
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
