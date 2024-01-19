<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    #[Route('/index', name : 'index')]
    public function indexCart(SessionInterface $session, ProductsRepository $productsRepository)
    {
        $cart = $session->get('cart', []);
        
        $data = [];
        $total = 0;

        foreach($cart as $id => $quantity) {
            $products = $productsRepository->find($id);
            $data[] = [
                'products' => $products,
                'quantity' => $quantity
            ];
            $total += $products->getPrice() * $quantity;
        }
        return $this->render('cart/index.html.twig', compact('data', 'total'));
    }

    #[Route('/add/{id}', name: 'add')]
    public function addCart(Products $products, SessionInterface $session)
    {
        $id = $products->getId();
        $cart = $session->get('cart', []);

        if (empty($cart[$id])) {
            $cart[$id] = 1;
        } else {
            $cart[$id]++;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Products $products, SessionInterface $session)
    {
        $id = $products->getId();

        $cart = $session->get('cart', []);

        if(!empty($cart[$id])){
            if($cart[$id] > 1){
                $cart[$id]--;
            }else{
                unset($cart[$id]);
            }
        }

        $session->set('cart', $cart);
        
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products $products, SessionInterface $session)
    {
        $id = $products->getId();

        $cart = $session->get('cart', []);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {
        $session->remove('cart');

        return $this->redirectToRoute('app_cart_index');
    }
}
