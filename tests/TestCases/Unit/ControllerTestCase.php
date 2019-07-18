<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\TestCases\Unit;

use LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface;
use LoyaltyCorp\RequestHandlers\Serializer\PropertyNormalizer;
use Tests\LoyaltyCorp\Multitenancy\TestCases\TestCase;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren) All tests extend this class
 */
class ControllerTestCase extends TestCase
{
    /**
     * Returns an unvalidated request object. The request object is not valid and may
     * cause fatal errors.
     *
     * THESE OBJECTS ARE ONLY FOR USE IN CONTROLLER UNIT TESTS!
     *
     * @param string $dtoClass
     * @param mixed[] $properties
     *
     * @return \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    protected function buildUnvalidatedRequestObject(string $dtoClass, array $properties): RequestObjectInterface
    {
        /** @var \Symfony\Component\Serializer\Serializer $serializer */
        $serializer = $this->getContainer()->get('requesthandlers_serializer');
        /** @var \LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface $instance */
        $instance = $serializer->denormalize([], $dtoClass, null, [
            PropertyNormalizer::EXTRA_PARAMETERS => $properties
        ]);

        return $instance;
    }
}
