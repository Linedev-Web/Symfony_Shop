<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{

    private RequestStack $requestStack;

    private EntityManagerInterface $entityManager;

    /**
     * @param $requestStack
     * @param $entityManager
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $id
     * @return void
     */
    public function add($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->requestStack->getSession()->set('cart', $cart);

    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->requestStack->getSession()->get('cart');
    }

    /**
     * @return mixed
     */
    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    public function decrease($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        return $this->requestStack->getSession()->set('cart', $cart);

    }

    public function getFull()
    {
        $cartComplete = [];

        foreach ($this->get() ?? [] as $id => $quantity) {
            $productObject = $this->entityManager->getRepository(Product::class)->findOneById($id);
            if (!$productObject) {
                $this->delete($id);
                continue;
            }

            $cartComplete[] = [
                'product' => $productObject,
                'quantity' => $quantity,
            ];
        }
        return $cartComplete;
    }

    public function delete($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        unset($cart[$id]);

        return $this->requestStack->getSession()->set('cart', $cart);
    }
}