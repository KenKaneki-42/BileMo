<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Phone ;

class PhoneController extends AbstractController
{
    #[Route('/api/phones', name: 'phones', methods: ['GET'])]
    public function getAllPhones(PhoneRepository $phoneRepository, SerializerInterface $serializer): JsonResponse
    {
        $phoneList = $phoneRepository->findAll();
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
