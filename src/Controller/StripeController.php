<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(CartController $cartController, ProductsRepository $productsRepository, SessionInterface $session): Response
    {
        $total = $cartController->getCartTotal($session, $productsRepository);

        return $this->render('stripe/index.html.twig', [
            'total' => $total,
            'stripe_key' => $_ENV["STRIPE_KEY"]
        ]);
    }

    #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request, CartController $cartController, ProductsRepository $productsRepository, SessionInterface $session)
    {
        $total = $cartController->getCartTotal($session, $productsRepository);
        $description = $cartController->getCartDescription($session, $productsRepository);

        $productDescriptions = [];
        foreach ($description as $item) {
            $productName = $item['products']->getName();
            $quantity = $item['quantity'];
            $productDescriptions[] = "$productName x $quantity";
        }

        $descriptionString = implode(', ', $productDescriptions);

        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create([
            "amount" => $total * 100,
            "currency" => "EUR",
            "source" => $request->request->get('stripeToken'),
            "description" => $descriptionString,
            
            "shipping" => [
                "address" => [
                    "line1" => $request->request->get('address_line1'),
                    "city" => $request->request->get('address_city'),
                    "country" => $request->request->get('address_country'),
                ],
                "name" => $request->request->get('shipping_name'),
            ],
        ]);

        $this->addFlash(
            'success',
            'Payment successful! Your order is being prepared, and a confirmation email will be sent shortly.'
        );

        // Videz le panier
        $session->remove('cart');

        // Redirigez vers la page d'accueil
        return $this->redirectToRoute('app_home');
    }
}
