<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\SerializerInterface;
use App\Entity\Phone ;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class PhoneController extends AbstractController
{
    #[Route('/api/phones', name: 'phones', methods: ['GET'])]
    public function getAllPhones(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->query->get('page') ?? 1;
        $limit = $request->query->get('limit') ?? 10;

        $idCache = 'getAllPhones' . $page . '_' . $limit;

        $jsonPhoneList = $cachePool->get($idCache, function(ItemInterface $item) use ($phoneRepository, $page, $limit, $serializer) {
          $item->tag('phonesCache');
          $item->expiresAfter(120);
          $phoneList = $phoneRepository->findAllwithPagination($page, $limit);
          return $serializer->serialize($phoneList, 'json');
        });

        return new JsonResponse($jsonPhoneList, 200, [], true);
    }

    #[Route('/api/phones/{id}', name: 'phone', methods: ['GET'])]
    public function getPhoneDetails(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $jsonPhone = $serializer->serialize($phone, 'json');
        return new JsonResponse($jsonPhone, 200, [], true);
    }
}
