<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use App\Entity\Phone ;
use App\Service\VersioningService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;

class PhoneController extends AbstractController
{
    #[Route('/api/phones', name: 'phones_list', methods: ['GET'])]
    #[OA\Tag(name: 'Phones')]
    #[ApiSecurity(name: 'Bearer')]
    public function getAllPhones(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool, VersioningService $versioningService): JsonResponse
    {
        $page = $request->query->get('page') ?? 1;
        $limit = $request->query->get('limit') ?? 10;

        $idCache = 'getAllPhones' . $page . '_' . $limit;

        $jsonPhoneList = $cachePool->get($idCache, function(ItemInterface $item) use ($phoneRepository, $page, $limit, $serializer, $versioningService) {
          $version = $versioningService->getVersion();
          $item->tag('phonesCache');
          $item->expiresAfter(120);
          $phoneList = $phoneRepository->findAllwithPagination($page, $limit);
          $context = SerializationContext::create()->setVersion($version);
          return $serializer->serialize($phoneList, 'json', $context);
        });

        return new JsonResponse($jsonPhoneList, 200, [], true);
    }

    #[Route('/api/phones/{id}', name: 'phone_details', methods: ['GET'])]
    #[OA\Tag(name: 'Phones')]
    #[ApiSecurity(name: 'Bearer')]
    public function getPhoneDetails(Phone $phone, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse
    {
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()->setVersion($version);
        $jsonPhone = $serializer->serialize($phone, 'json');
        return new JsonResponse($jsonPhone, 200, [], true);
    }
}
