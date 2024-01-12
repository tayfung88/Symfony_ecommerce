<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ProductsController extends AbstractController
{
    #[Route('/products/list', name: 'app_list_products')]
    public function listProduct(EntityManagerInterface $entityManager)
    {
        $products = $entityManager->getRepository(Products::class)->findAll();
        $categories = $entityManager->getRepository(Categories::class)->findAll();

        return $this->render('products/list.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }


    #[Route('/products/add', name: 'app_add_products' )]
    public function addProductForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        $products = new Products();
        $productsForm = $this->createForm(ProductsFormType::class, $products);

        $productsForm->handleRequest($request);
        if ($productsForm->isSubmitted() && $productsForm->isValid()) {

            $products = $productsForm->getData();

            $entityManager->persist($products);
            $entityManager->flush();

            $this->addFlash('success', 'Product added successfully!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('products/add.html.twig', [
            'productsForm' => $productsForm
        ]);
    }

    
    #[Route('/products/edit/{id}', name: 'app_edit_products')]
    public function editProductForm(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $products = $entityManager->getRepository(Products::class)->find($id);
        $productsForm = $this->createForm(ProductsFormType::class, $products);
        $productsForm->handleRequest($request);

        if ($productsForm->isSubmitted() && $productsForm->isValid()) {

            $products = $productsForm->getData();

            $entityManager->persist($products);
            $entityManager->flush();

            $this->addFlash('success', 'Product edited successfully!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('products/edit.html.twig', [
            'productsForm' => $productsForm
        ]);
    }

    #[Route('/products/delete/{id}', name: 'app_delete_products')]
    public function deleteProductForm(EntityManagerInterface $entityManager, int $id): Response
    {
        $products = $entityManager->getRepository(Products::class)->find($id);
        $entityManager->remove($products);
        $entityManager->flush();

        $this->addFlash('success', 'Product deleted successfully!');

        return $this->redirectToRoute('app_home');
    }

}
