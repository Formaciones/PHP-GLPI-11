<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/home', name: 'Home')]
final class HomeController extends AbstractController
{
    #[Route('/index/{nombre}', 
        name: '-Index', 
        methods: ['GET', 'POST'], 
        defaults: ['nombre' => 'Adrian'],
        requirements: ['nombre' => '\w+'])]
    public function index(Request $request, string $nombre): Response
    {
        // Parámetro URL o Campo de un formulario método GET
        $ciudad = $request->query->get('ciudad');

        // Campo de un formulario método POST
        //$ciudad = $request->get('ciudad');


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'usuario' => $nombre,
            'ciudad' => $ciudad,
        ]);
    }

    
    #[Route('/listado', name: '-Listado', methods: ['GET'])]
    public function listado(Request $request): Response
    {
        return $this->render('home/listado.html.twig', []);
    } 
}
