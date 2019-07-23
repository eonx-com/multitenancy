<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Http\Controllers;

use EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest;
use LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderModifyRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class ProviderController extends BaseController
{
    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructs a new instance of ProviderController.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * Creates a new provider.
     *
     * @param \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest $request
     *
     * @return \EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface
     */
    public function create(ProviderCreateRequest $request): FormattedApiResponseInterface
    {
        $provider = new Provider($request->getId(), $request->getName());

        $this->entityManager->persist($provider);
        $this->entityManager->flush();

        return $this->formattedApiResponse($provider, 201);
    }

    /**
     * Modifies a provider.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderModifyRequest $request
     *
     * @return \EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface
     *
     * @Entity(name="provider", expr="repository.findOneBy({id:id})")
     */
    public function modify(Provider $provider, ProviderModifyRequest $request): FormattedApiResponseInterface
    {
        $provider->setName($request->getName());

        $this->entityManager->persist($provider);
        $this->entityManager->flush();

        return $this->formattedApiResponse($provider, 200);
    }
}
