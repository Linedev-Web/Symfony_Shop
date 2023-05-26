<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Card;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if (!$order->isIsPaid()) {
            $cart->remove();
            $order->setIsPaid(1);
            $this->entityManager->flush();


            $mail = new Mail();
            $content = "Bonjour ". $order->getUser()->getFirstName().'<br> dqzdqz dqdz dqzdqz dqzdz dqz dzq ';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande sur le site est valider', $content);
        }


        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
