<?php
declare(strict_types=1);


namespace App\Resolvers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use App\DTO\PropertyCreateDto;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Uid\Uuid;

class CreatePropertyResolver implements ValueResolverInterface
{
    public function __construct()
    {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== PropertyCreateDto::class) {
            return [];
        }
        $dto = new PropertyCreateDto(
            $request->request->get('propertyTypeId'),
            Uuid::fromString($request->request->get('agentId')),
            floatval($request->request->get('priceAmount')),
            $request->request->get('priceCurrencyId'),
            floatval($request->request->get('latitude')),
            floatval($request->request->get('longitude')),
            $request->request->get('address'),
            floatval($request->request->get('area')),
            $request->request->get('measurement'),
            $request->request->get('description'),
            $request->files->get('images')? $request->files->get('images'): null,
        );
        return [$dto];
    }
}
