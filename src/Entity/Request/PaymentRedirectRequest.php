<?php

declare(strict_types=1);

namespace Paysera\CheckoutSdk\Entity\Request;

use Paysera\CheckoutSdk\Entity\Order;
use Paysera\CheckoutSdk\Entity\RequestInterface;

/**
 * This entity provides the necessary integration fields for payment redirect.
 * @link https://developers.paysera.com/en/checkout/integrations/integration-specification
 */
class PaymentRedirectRequest implements RequestInterface
{
    /**
     * Unique project number. Only activated projects can accept payments.
     */
    private int $projectId;

    /**
     * Unique project password.
     */
    private string $projectPassword;

    /**
     * Full address (URL), to which the client is directed after a successful payment.
     */
    private string $acceptUrl;

    /**
     * Full address (URL), to which the client is directed after he clicks the link to return to the shop.
     */
    private string $cancelUrl;

    /**
     * Full address (URL), to which a seller will get information about performed payment.
     * Script must return text "OK".
     */
    private string $callbackUrl;

    /**
     * The version number of Paysera system specification (API).
     */
    private ?string $version;

    /**
     * It is possible to indicate the user language (ISO 639-2/B: LIT, RUS, ENG, etc.).
     * If Paysera does not support the selected language, the system will automatically choose a language
     * according to the IP address or ENG language by default.
     */
    private ?string $language;

    /**
     * Payment type.
     * If provided, the payment will be made by the method specified (for example by using the specified bank).
     * If not specified, the payer will be immediately provided with the payment types to choose from.
     * You can get payment types in real time by using this library.
     */
    private ?string $payment;

    /**
     * Payer's country (LT, EE, LV, GB, PL, DE).
     * All possible types of payment in that country are immediately indicated to the payer,
     * after selecting a country.
     */
    private ?string $country;

    /**
     * Payment purpose visible when making the payment.
     * If not specified, default text is used: Payment for goods and services (for nb. [order_nr]) ([site_name]).
     * If you specify the payment purpose, it is necessary to include the following variables,
     * which will be replaced with the appropriate values in the final purpose text:
     *     [order_nr] - payment number.
     *     [site_name] or [owner_name] - website address or company name.
     */
    private ?string $paymentText;

    /**
     * The parameter, which allows to test the connection.
     * The payment is not executed, but the result is returned immediately, as if the payment has been made.
     * To test, it is necessary to activate the mode for a particular project by logging in and selecting:
     *     "Projects and Activities" -> "My projects" -> "Project settings"
     *         -> "Payment collection service settings" -> "Allow test payments" (check).
     */
    private bool $test;

    /**
     * If it is enabled, the payer can skip the additional step where he needs to accept consent using PIS payment.
     * If this parameter is used, additional text is required to be added to your page (described in the documentation):
     *     @link https://developers.paysera.com/en/checkout/integrations/integration-specification
     */
    private bool $buyerConsent;

    /**
     * The parameter indicating the final date for payment; the date is given in “yyyy-mm-dd HH:MM:SS” format.
     * The minimum value is 15 minutes from the current moment; the maximum value is 3 days.
     * Note: works only with certain payment methods.
     */
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
        $this->version = null;
        $this->payment = null;
        $this->language = null;
        $this->country = null;
        $this->paymentText = null;
        $this->test = false;
        $this->buyerConsent = false;
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

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

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

    public function getBuyerConsent(): bool
    {
        return $this->buyerConsent;
    }

    public function setBuyerConsent(?bool $buyerConsent): self
    {
        $this->buyerConsent = $buyerConsent ?? false;

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
