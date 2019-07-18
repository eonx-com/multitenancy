<?php
declare(strict_types=1);

namespace LoyaltyCorp\Mulitenancy\Http\Requests\Providers;

use LoyaltyCorp\Mulitenancy\Http\Exceptions\Requests\ProviderRequestValidationException;
use LoyaltyCorp\Mulitenancy\Http\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\GroupSequence({"ProviderCreateRequest", "SecondPass"})
 */
class ProviderCreateRequest extends BaseRequest
{
    /**
     * The identifier of the provider.
     *
     * @Assert\Length(max=100)
     * @Assert\NotBlank()
     * @Assert\Type(type="string", groups={"PreValidate"})
     *
     * @var string
     */
    private $id;

    /**
     * The name of the provider.
     *
     * @Assert\Length(max=255)
     * @Assert\NotBlank()
     * @Assert\Type("string", groups={"PreValidate"})
     *
     * @var string
     */
    private $name;

    /**
     * Returns the exception class to be used when a failure occurs trying to deserialise
     * and validate the request object.
     *
     * @return string
     */
    public static function getExceptionClass(): string
    {
        return ProviderRequestValidationException::class;
    }

    /**
     * Gets the provider id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Gets the name of the provider.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}