<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CustomerRepository;

#[Route('/clientes', name: 'Customer')]
final class CustomerController extends AbstractController
{
    #[Route('/listado', name: ' Listado')]
    public function index(CustomerRepository $repository): Response
    {
        $clientes = $repository->findAll();

        return $this->render('customer/index.html.twig', [
            'clientes' => $clientes,
        ]);
    }

    #[Route('/ficha/{id}', name: ' Ficha')]
    public function ficha(CustomerRepository $repository, string $id): Response
    {
        $cliente = $repository->findOneBy(['CustomerID' => $id]);

        return $this->render('customer/ficha.html.twig', [
            'cliente' => $cliente,
        ]);
    }
}
