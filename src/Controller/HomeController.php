<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ProductRepository $productRepository;
    private PaginatorInterface $paginator;

    public function __construct(ProductRepository $productRepository, PaginatorInterface $paginator)
    {
        $this->productRepository = $productRepository;
        $this->paginator = $paginator;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        // Récupération de la query (SELECT * FROM product)
        $qb = $this->productRepository->getQbAll();

        $pagination = $this->paginator->paginate(
            // la query
            $qb,
            // Récupération du paramètre de l'URL
            $request->query->getInt('page', 1),
            // Nombre de résultat par page
            9
        );

        return $this->render('home/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
