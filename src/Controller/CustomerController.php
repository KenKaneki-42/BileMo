<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Entity\Customer ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use App\Service\VersioningService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use OpenApi\Attributes as OA;
use Symfony\Bundle\SecurityBundle\Security;


class CustomerController extends AbstractController
{
    #[Route('/api/customers', name: 'customers_list_for_user', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Retourne des customers',content: new OA\JsonContent(properties: [new OA\Property(property: 'customer1',ref: new Model(type: Customer::class, groups: ['getCustomerDetails'])),new OA\Property(property: 'customer2', ref: new Model(type: Customer::class, groups: ['getCustomerDetails']))]))]
    #[OA\Parameter(name: 'page', in: 'query', description: "La page que l'on veut récupérer", schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'limit', in: 'query', description: "Le nombre d'éléments que l'on veut récupérer", schema: new OA\Schema(type: 'string'))]
    #[OA\Tag(name: 'Customers')]
    #[ApiSecurity(name: 'Bearer')]
    public function getCustomersForUser(Security $security, CustomerRepository $customerRepository, SerializerInterface $serializer, VersioningService $versioningService): JsonResponse
    {
        $user = $security->getUser();
        if (!$user){
          // 401 = JsonResponse::HTTP_UNAUTHORIZED
          return new JsonResponse('Unauthorized', 401, [], true);
        }
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()
          ->setGroups(['getCustomerDetails'])
          ->setVersion($version);
        $customerList = $customerRepository->findBy(['user' => $user]);
        $jsonCustomerList = $serializer->serialize($customerList, 'json', $context);
        return new JsonResponse($jsonCustomerList, 200, [], true);
    }

    #[Route('/api/customers/{id}', name: 'customer_details', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Retourne un customer', content: new OA\JsonContent(properties: [new OA\Property(property: 'customer',ref: new Model(type: Customer::class, groups: ['getCustomerDetails']))]))]
    #[OA\Tag(name: 'Customers')]
    #[ApiSecurity(name: 'Bearer')]
    public function getCustomerDetails(Customer $customer, SerializerInterface $serializer, VersioningService $versioningService, Security $security): JsonResponse
    {
        $user = $security->getUser();
        if ($customer->getUser() !== $user){
            // 403 = JsonResponse::HTTP_FORBIDDEN
            return new JsonResponse('Forbidden', 403, [], true);
        }
        $version = $versioningService->getVersion();
        $context = SerializationContext::create()
        ->setGroups(['getCustomerDetails'])
        ->setVersion($version);
        $jsonPhone = $serializer->serialize($customer, 'json', $context);
        // 200 = JsonResponse::HTTP_OK
        return new JsonResponse($jsonPhone, 200, [], true);
    }

    #[Route('/api/customers/new', name: 'create_customer', methods: ['POST'])]
    #[OA\Tag(name: 'Customers')]
    #[ApiSecurity(name: 'Bearer')]
    #[OA\RequestBody(request: "CreateCustomer",description: "Créer un nouveau customer",required: true,content: new OA\JsonContent(type: "object",required: ["name", "email"],properties: [new OA\Property(property: "firstName", type: "string", example: "Doe"),new OA\Property(property: "lastName", type: "string", example: "John"),new OA\Property(property: "email", type: "string", format: "email", example: "john.doe@example.com")]))]
    public function createCustomer(Security $security, Request $request, SerializerInterface $serializer,EntityManagerInterface $em, ValidatorInterface $validator, VersioningService $versioningService, TagAwareCacheInterface $cachePool): JsonResponse
    {
        if (!$security->isGranted('ROLE_USER')) {
            // 403 = JsonResponse::HTTP_FORBIDDEN
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $version = $versioningService->getVersion();

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $customer
          ->setCreatedAt(new \DateTimeImmutable('now'))
          ->setUpdatedAt(new \DateTime('now'));

        $customer->setUser($security->getUser());

        $errors = $validator->validate($customer);
        if (count($errors) > 0) {
            // 400 = JsonResponse::HTTP_BAD_REQUEST
            return new JsonResponse($serializer->serialize($errors,'json'), 400, [], true);
        }

        $em->persist($customer);
        $em->flush();

        $cachePool->invalidateTags(['customersCache']);

        $context = SerializationContext::create()
          ->setGroups(['getCustomerDetails'])
          ->setVersion($version);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);

        $location = $this->generateUrl('customerDetails', ['id' => $customer->getId()]);

        // 201 = JsonResponse::HTTP_CREATED
        return new JsonResponse($jsonCustomer, 201, ["Location" => $location], true);
    }

    #[Route('/api/customers/{id}', name: 'delete_customer', methods: ['DELETE'])]
    #[OA\Tag(name: 'Customers')]
    #[ApiSecurity(name: 'Bearer')]
    #[OA\Response(response: 204,description: "Le customer a été supprimé avec succès.")]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em, TagAwareCacheInterface $cachePool, Security $security): JsonResponse
    {
          if (!$security->isGranted('ROLE_USER')) {
            // 403 = JsonResponse::HTTP_FORBIDDEN
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $cachePool->invalidateTags(['customersCache']);
        $em->remove($customer);
        $em->flush();
        // 204 = JsonResponse::HTTP_NO_CONTENT
        return new JsonResponse('Customer supprimé!', 204, [], true);
    }


}
