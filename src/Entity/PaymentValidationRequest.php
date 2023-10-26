<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity;

class PaymentValidationRequest implements RequestInterface
{
    private int $projectId;
    private string $projectPassword;
    private string $data;
    private ?string $ss1;
    private ?string $ss2;
    private ?string $type;
    private ?string $to;
    private ?string $from;

    /**
     * @var array<int|string, mixed>|null
     */
    private ?array $sms;

    public function __construct(
        int $projectId,
        string $projectPassword,
        string $data
    ) {
        $this->projectId = $projectId;
        $this->projectPassword = $projectPassword;
        $this->data = $data;
        $this->ss1 = null;
        $this->ss2 = null;
        $this->type = null;
        $this->to = null;
        $this->from = null;
        $this->sms = null;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getProjectPassword(): string
    {
        return $this->projectPassword;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getSs1(): ?string
    {
        return $this->ss1;
    }

    public function setSs1(?string $ss1): self
    {
        $this->ss1 = $ss1;

        return $this;
    }

    public function getSs2(): ?string
    {
        return $this->ss2;
    }

    public function setSs2(?string $ss2): self
    {
        $this->ss2 = $ss2;

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

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(?string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setFrom(?string $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return array<int|string, mixed>|null
     */
    public function getSms(): ?array
    {
        return $this->sms;
    }

    /**
     * @param array<int|string, mixed>|null $sms
     * @return $this
     */
    public function setSms(?array $sms): self
    {
        $this->sms = $sms;

        return $this;
    }
}
