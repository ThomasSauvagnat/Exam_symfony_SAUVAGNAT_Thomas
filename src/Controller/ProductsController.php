<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductUpdateType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/produits', name: 'app_products')]
    public function index(UserRepository $userRepository): Response
    {
        $userEntity = $this->getUser();
        $productsUser = $userEntity->getProducts();
        dump(count($productsUser));

        return $this->render('products/index.html.twig', [
            'productsUser' => $productsUser,
        ]);
    }

    #[Route('/produits/details/{id}', name: 'app_products_details')]
    public function detailsProduct(Product $product ,UserRepository $userRepository): Response
    {
        return $this->render('products/detailsProduct.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/produits/ajouter', name: 'app_product_add')]
    public function addProduct(Request $request , EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        // Récupération de l'utilisateur
        $userEntity = $this->getUser();
        // Création du formulaire
        $form = $this->createForm(ProductType::class, new Product());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $newProductEntity = $form->getData();
            $newProductEntity->setCreatedAt(new \DateTime());
            $newProductEntity->setIsActive(true);
            $newProductEntity->setCreatedBy($userEntity);
            $entityManager->persist($newProductEntity);
            $entityManager->flush();
            return $this->redirectToRoute('app_products');
        }

        return $this->render('products/addProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/produits/modifier/{id}', name: 'app_product_update')]
    public function updateProduct(Product $product ,Request $request , EntityManagerInterface $entityManager): Response
    {
        $userEntity = $this->getUser();
        $form = $this->createForm(ProductUpdateType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if ($userEntity === $product->getCreatedBy()) {
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirectToRoute('app_products');
            }
        }

        return $this->render('products/updateProduct.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/produits/supprimer/{id}', name: 'app_product_delete')]
    public function deleteProduct(Product $product, EntityManagerInterface $entityManager): Response
    {
        $userEntity=$this->getUser();

        if ($userEntity === $product->getCreatedBy()) {
            $entityManager->remove($product);
            $entityManager->flush();
            return $this->redirectToRoute('app_products');
        }
        return $this->redirectToRoute('app_products');
    }

    #[Route('/produits/actif/{id}', name: 'app_product_isActiveOrNot')]
    public function isActiveProduct(Product $product, EntityManagerInterface $entityManager): Response
    {
        $product->setIsActive(false);
        $entityManager->persist($product);
        $entityManager->flush();
        return $this->redirectToRoute('app_home');
    }
}
