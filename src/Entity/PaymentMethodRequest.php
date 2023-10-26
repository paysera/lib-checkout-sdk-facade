<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

class PaymentMethodRequest implements RequestInterface
{
    private int $projectId;
    private string $language;
    private array $selectedCountries;
    private Order $order;

    public function __construct(
        int $projectId,
        string $language,
        Order $order,
        array $selectedCountries = []
    ) {
        $this->projectId = $projectId;
        $this->language = $language;
        $this->order = $order;
        $this->selectedCountries = $selectedCountries;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return array<int, string>
     */
    public function getSelectedCountries(): array
    {
        return $this->selectedCountries;
    }
}
