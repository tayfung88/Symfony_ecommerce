<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $cart = $session->get('cart', []);
        // dd($cart);

        if($cart === []){
            $this->addFlash('cart_empty', 'Your cart is empty');
            return $this->redirectToRoute('app_home');
        }

        // Le panier n'est pas vie, on crée la commande
        $order = new Orders();

        // On rempli la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());
        
        // On parcourt le panier pour créer les détails de commande
        foreach($cart as $item => $quantity){
            $orderDetails = new OrdersDetails();

            // On va chercher le produit
            $product = $productsRepository->find($item);
            $price = $product->getPrice();

            // On crée le detail de commande
            $orderDetails->setProducts($product);
            $orderDetails->setPrice($price);
            $orderDetails->setQuantity($quantity);

            $order->addOrdersDetail($orderDetails);
        }

        // On persiste et on flush (On créer les requetes et on les éxécute)
        $entityManager->persist($order);
        $entityManager->flush();

        $session->remove('cart');

        $this->addFlash('order_success', 'Order created successfully');
        return $this->redirectToRoute('app_home');
    }
}
