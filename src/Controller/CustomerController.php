<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Entity\User ;
use App\Entity\Customer ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use App\Service\VersioningService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;

class CustomerController extends AbstractController
{
    #[Route('/api/users/{id}/customers', name: 'customersListForUser', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne la liste des customers',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Customer::class, groups: ['getCustomerDetails']))
        )
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: "La page que l'on veut récupérer",
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: "Le nombre d'éléments que l'on veut récupérer",
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Tag(name: 'customers')]
    // TODO si j'utilise le Security, je n'ai pas accès à ma route custom, l'autorisation ne passe pas
    // #[Security(name: 'Bearer')]
    public function getCustomersForUser(User $user, CustomerRepository $customerRepository, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse
    {   $version = $versioningService->getVersion();
        $context = SerializationContext::create()
          ->setGroups(['getCustomerDetails'])
          ->setVersion($version);
        $customerList = $customerRepository->findBy(['user' => $user]);
        $jsonCustomerList = $serializer->serialize($customerList, 'json', $context);
        return new JsonResponse($jsonCustomerList, 200, [], true);
    }

    #[Route('/api/customers/{id}', name: 'customerDetails', methods: ['GET'])]
    public function getCustomerDetails(Customer $customer, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse
    {
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()
        ->setGroups(['getCustomerDetails'])
        ->setVersion($version);
        $jsonPhone = $serializer->serialize($customer, 'json', $context);
        // 200 = JsonResponse::HTTP_OK
        return new JsonResponse($jsonPhone, 200, [], true);
    }

    #[Route('/api/users/{id}/customers', name: 'createCustomer', methods: ['POST'])]
    public function createCustomer(User $user, Request $request, SerializerInterface $serializer,EntityManagerInterface $em, ValidatorInterface $validator, VersioningService $versioningService): JsonResponse
    {
        $version = $versioningService->getVersion();

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setUser($user);

        $errors = $validator->validate($customer);
        if (count($errors) > 0) {
            // 400 = JsonResponse::HTTP_BAD_REQUEST
            return new JsonResponse($serializer->serialize($errors,'json'), 400, [], true);
            // throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "La requête est invalide");
        }

        $em->persist($customer);
        $em->flush();
        $context = SerializationContext::create()
          ->setGroups(['getCustomerDetails'])
          ->setVersion($version);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);

        $location = $this->generateUrl('customerDetails', ['id' => $customer->getId()]);

        // 201 = JsonResponse::HTTP_CREATED
        return new JsonResponse($jsonCustomer, 201, ["Location" => $location], true);
    }

    #[Route('/api/customers/{id}', name: 'deleteCustomer', methods: ['DELETE'])]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $cachePool->invalidateTags(['customersCache']);
        $em->remove($customer);
        $em->flush();
        // 204 = JsonResponse::HTTP_NO_CONTENT
        return new JsonResponse('Customer deleted!', 204, [], true);
    }


}
