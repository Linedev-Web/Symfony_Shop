<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    public EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/commande/stripe', name: 'order_stipe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/commande/stripe/payment/{reference}', name: 'order_stipe_payment')]
    public function payment($reference): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference);

        if (!$order) {
            return $this->redirectToRoute('order');
        }

        $products_for_stripe = [];
        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $this->entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => ['http://localhost:8000/uploads' . $product_object->getIllustration()]
                    ],
                    'unit_amount' => $product->getPrice(),
                ],
                'quantity' => $product->getQuantity(),
            ];
        }
        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => ['https://png.pngtree.com/png-clipart/20190520/original/pngtree-transport-icon-png-image_3606057.jpg']
                ],
                'unit_amount' => $order->getCarrierPrice() * 100,
            ],
            'quantity' => 1,
        ];

        $stripe = new \Stripe\StripeClient('sk_test_51DLAc6D6VULew1vaLs5WybjJwvFuzM6uMPL7h5Sz3XeuqrARVPkVYCfjqIpMOP3GnvY6K7mNTgGNAid89UTZlaPZ00KLGgRpj9');
        header('Content-Type: application/json');

        $checkout_session = $stripe->checkout->sessions->create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [$products_for_stripe],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/success',
            'cancel_url' => 'http://localhost:8000/cancel',
        ]);

        return new RedirectResponse($checkout_session->url);
    }
}
