<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
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
        $phoneList = $cachePool->get($idCache, function(ItemInterface $item) use ($phoneRepository, $page, $limit) {
          $item->tag('phonesCache');
          return $phoneRepository->findAllwithPagination($page, $limit);
        });
        // $phoneList = $phoneRepository->findAllwithPagination($page, $limit);
        $jsonPhoneList = $serializer->serialize($phoneList, 'json');
        return new JsonResponse($jsonPhoneList, 200, [], true);
    }

    #[Route('/api/phones/{id}', name: 'phone', methods: ['GET'])]
    public function getPhoneDetails(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $jsonPhone = $serializer->serialize($phone, 'json');
        return new JsonResponse($jsonPhone, 200, [], true);
    }
}
