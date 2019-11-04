<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Http\Controllers;

use EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest;
use LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderModifyRequest;
use LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

final class ProviderController extends BaseController
{
    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface
     */
    private $providerService;

    /**
     * Constructs a new instance of ProviderController.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface $entityManager
     * @param \LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface $providerService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProviderServiceInterface $providerService
    ) {
        $this->entityManager = $entityManager;
        $this->providerService = $providerService;
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
        $provider = $this->providerService->createOrFind($request->getId(), $request->getName());

        $this->entityManager->flush();

        return $this->formattedApiResponse($this->buildResponse($provider), 201);
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

        return $this->formattedApiResponse($this->buildResponse($provider), 200);
    }

    /**
     * Builds the basic Provider response.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     *
     * @return mixed[]
     */
    private function buildResponse(Provider $provider): array
    {
        return [
            'id' => $provider->getExternalId(),
            'name' => $provider->getName(),
        ];
    }
}
