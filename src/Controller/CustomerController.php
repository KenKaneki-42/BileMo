<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User ;
use App\Entity\Customer ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends AbstractController
{
    #[Route('/api/users/{id}/customers', name: 'customersListForUser', methods: ['GET'])]
    public function getCustomersForUser(User $user, CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customerList = $customerRepository->findBy(['user' => $user]);
        $jsonCustomerList = $serializer->serialize($customerList, 'json', ['groups' => 'getCustomerDetails']);
        return new JsonResponse($jsonCustomerList, 200, [], true);
    }

    #[Route('/api/customers/{id}', name: 'customerDetails', methods: ['GET'])]
    public function getCustomerDetails(Customer $customer, SerializerInterface $serializer): JsonResponse
    {

        $jsonPhone = $serializer->serialize($customer, 'json', ['groups' => 'getCustomerDetails']);
        // 200 = JsonResponse::HTTP_OK
        return new JsonResponse($jsonPhone, 200, [], true);
    }

    #[Route('/api/users/{id}/customers', name: 'createCustomer', methods: ['POST'])]
    public function createCustomer(User $user, Request $request, SerializerInterface $serializer,EntityManagerInterface $em): JsonResponse
    {
        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');
        $customer->setUser($user);
        $em->persist($customer);
        $em->flush();

        $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomerDetails']);

        $location = $this->generateUrl('customerDetails', ['id' => $customer->getId()]);

        // 201 = JsonResponse::HTTP_CREATED
        return new JsonResponse($jsonCustomer, 201, ["Location" => $location], true);
    }

    #[Route('/api/customers/{id}', name: 'delete_customer', methods: ['DELETE'])]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($customer);
        $em->flush();
        // 204 = JsonResponse::HTTP_NO_CONTENT
        return new JsonResponse('Customer deleted!', 204, [], true);
    }


}
