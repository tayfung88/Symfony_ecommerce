<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Products;
use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;


class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager)
    {
        $promotedProducts = $entityManager->getRepository(Products::class)->findBy(['promotion' => true]);
        $products = $entityManager->getRepository(Products::class)->findAll();
        $categories = $entityManager->getRepository(Categories::class)->findAll();

        return $this->render('home/index.html.twig', [
            'promotion' => $promotedProducts,
            'products' => $products,
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category')]
    public function category($id, EntityManagerInterface $entityManager)
    {
        $category = $entityManager->getRepository(Categories::class)->find($id);
        $productsByCategory = $entityManager->getRepository(Products::class)->findBy(['category' => $category]);

        return $this->render('category/categories.html.twig', [
            'category' => $category,
            'products' => $productsByCategory
        ]);
    }




    //////////////////////// test nav on all pages ::

    #[Route('/navCategories', name:'nav_categories')]
    public function navCategories(EntityManagerInterface $entityManager)
    {

        $products = $entityManager->getRepository(Products::class)->findAll();
        $categories = $entityManager->getRepository(Categories::class)->findAll();

        return $this->render('navbar.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    ///////////////////////////////////

}
