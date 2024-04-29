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

declare(strict_types=1);

namespace BaksDev\Orders\Order\Repository\CurrentOrderEvent;

use App\Kernel;
use BaksDev\Core\Doctrine\ORMQueryBuilder;
use BaksDev\Core\Entity\EntityTestGenerator;
use BaksDev\Orders\Order\Entity\Event\OrderEvent;
use BaksDev\Orders\Order\Entity\Order;
use BaksDev\Orders\Order\Type\Id\OrderUid;
use Doctrine\ORM\EntityManagerInterface;

final class CurrentOrderEventRepository implements CurrentOrderEventInterface
{
    private ORMQueryBuilder $ORMQueryBuilder;

    public function __construct(ORMQueryBuilder $ORMQueryBuilder)
    {
        $this->ORMQueryBuilder = $ORMQueryBuilder;
    }

    /**
     * Метод возвращает текущее активное событие заказа
     */
    public function getCurrentOrderEvent(Order|OrderUid|string|null $order): ?OrderEvent
    {

        if(Kernel::isTestEnvironment())
        {
            return EntityTestGenerator::get(OrderEvent::class);
        }

        if(!$order) { return null; }

        if($order instanceof Order)
        {
            $order = $order->getId();
        }

        if(is_string($order))
        {
            $order = new OrderUid($order);
        }

        $qb = $this->ORMQueryBuilder->createQueryBuilder(self::class);

        $qb->select('event');
        $qb->from(Order::class, 'orders');
        $qb->join(OrderEvent::class, 'event', 'WITH', 'event.id = orders.event');
        $qb->where('orders.id = :order');
        $qb->setParameter('order', $order, OrderUid::TYPE);

        return $qb->getOneOrNullResult();
    }

}