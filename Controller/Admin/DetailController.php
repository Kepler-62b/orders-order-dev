<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Orders\Order\Controller\Admin;

use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Services\Security\RoleSecurity;
use BaksDev\Orders\Order\Entity\Order;
use BaksDev\Orders\Order\Repository\CurrentOrderEvent\CurrentOrderEventInterface;
use BaksDev\Orders\Order\Repository\OrderDetail\OrderDetailInterface;
use BaksDev\Orders\Order\Repository\OrderHistory\OrderHistoryInterface;
use BaksDev\Orders\Order\Repository\ProductUserBasket\ProductUserBasketInterface;
use BaksDev\Orders\Order\Type\Status\OrderStatus\Collection\OrderStatusCollection;
use BaksDev\Orders\Order\UseCase\Admin\NewEdit\OrderDTO;
use BaksDev\Orders\Order\UseCase\Admin\NewEdit\OrderForm;
use BaksDev\Orders\Order\UseCase\Admin\NewEdit\OrderHandler;
use BaksDev\Orders\Order\UseCase\Admin\NewEdit\Products\OrderProductDTO;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[RoleSecurity('ROLE_ORDER')]
final class DetailController extends AbstractController
{
    #[Route('/admin/order/detail/{id}', name: 'admin.detail', methods: [
        'GET',
        'POST',
    ])]
    public function index(
        Request $request,
        #[MapEntity] Order $Order,
        CurrentOrderEventInterface $currentOrderEvent,
        ProductUserBasketInterface $userBasket,
        OrderDetailInterface $orderDetail,
        OrderHistoryInterface $orderHistory,
        OrderStatusCollection $collection,
        OrderHandler $handler,
        string $id,
    ): Response {
        /** Получаем активное событие заказа */
        $Event = $currentOrderEvent->getCurrentOrderEventOrNull($Order->getId());

        $OrderDTO = new OrderDTO();
        $Event->getDto($OrderDTO);

        /** @var OrderProductDTO $product */
        foreach ($OrderDTO->getProduct() as $product) {
            $ProductDetail = $userBasket->fetchProductBasketAssociative(
                $product->getProduct(),
                $product->getOffer(),
                $product->getVariation(),
                $product->getModification()
            );

            //			/dump($ProductDetail);

            $product->setCard($ProductDetail);
        }

        // $OrderDTO->setProduct($products);

        // dump($Event);
        // dd($OrderDTO);
        // Динамическая форма корзины
        $handleForm = $this->createForm(OrderForm::class, $OrderDTO);
        $handleForm->handleRequest($request);

        // форма заказа
        $form = $this->createForm(
            OrderForm::class,
            $OrderDTO,
            ['action' => $this->generateUrl('Orders:admin.detail', ['id' => $id])]
        );

        if (null === $request->headers->get('X-Requested-With')) {
            $form->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $Order = $handler->handle($OrderDTO);

            if ($Order instanceof Order) {
                $this->addFlash('success', 'admin.success.update', 'admin.order');
            } else {
                $this->addFlash('danger', 'admin.danger.update', 'admin.order', $Order);
            }

            return $this->redirectToRoute('Orders:admin.index');
        }

        /** Информация о заказе */
        $Order = $orderDetail->fetchDetailOrderAssociative($Event->getOrders());

        /** История изменения статусов */
        $History = $orderHistory->fetchHistoryAllAssociative($Event->getOrders());

        return $this->render(
            [
                'id' => $id,
                'form' => $form->createView(),
                'order' => $Order,
                'history' => $History,
                'status' => $collection->from($Order['order_status']),
                'statuses' => $collection,

                // 'query' =>  $orders,
                // 'status' => $collection->cases(),
                // 'search' => $searchForm->createView(),
            ]
        );
    }
}
