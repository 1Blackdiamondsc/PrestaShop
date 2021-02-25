<?php

namespace MolliePrefix\Mollie\Api\Resources;

class Method extends \MolliePrefix\Mollie\Api\Resources\BaseResource
{
    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;
    /**
     * More legible description of the payment method.
     *
     * @var string
     */
    public $description;
    /**
     * An object containing value and currency. It represents the minimum payment amount required to use this
     * payment method.
     *
     * @var \stdClass
     */
    public $minimumAmount;
    /**
     * An object containing value and currency. It represents the maximum payment amount allowed when using this
     * payment method.
     *
     * @var \stdClass
     */
    public $maximumAmount;
    /**
     * The $image->size1x and $image->size2x to display the payment method logo.
     *
     * @var \stdClass
     */
    public $image;
    /**
     * The issuers available for this payment method. Only for the methods iDEAL, KBC/CBC and gift cards.
     * Will only be filled when explicitly requested using the query string `include` parameter.
     *
     * @var array|object[]
     */
    public $issuers;
    /**
     * The pricing for this payment method. Will only be filled when explicitly requested using the query string
     * `include` parameter.
     *
     * @var array|object[]
     */
    public $pricing;
    /**
     * The activation status the method is in.
     *
     * @var string
     */
    public $status;
    /**
     * @var \stdClass
     */
    public $_links;
    /**
     * Get the issuer value objects
     *
     * @return IssuerCollection
     */
    public function issuers()
    {
        return \MolliePrefix\Mollie\Api\Resources\ResourceFactory::createBaseResourceCollection($this->client, \MolliePrefix\Mollie\Api\Resources\Issuer::class, $this->issuers);
    }
    /**
     * Get the method price value objects.
     *
     * @return MethodPriceCollection
     */
    public function pricing()
    {
        return \MolliePrefix\Mollie\Api\Resources\ResourceFactory::createBaseResourceCollection($this->client, \MolliePrefix\Mollie\Api\Resources\MethodPrice::class, $this->pricing);
    }
}