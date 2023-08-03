<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Orders\Order\Controller\Admin\Tests;

use BaksDev\Orders\Order\Entity\Order;
use BaksDev\Orders\Order\Type\Id\OrderUid;
use BaksDev\Orders\Order\Type\Status\OrderStatus\OrderStatusNew;
use BaksDev\Users\User\Tests\TestUserAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/** @group orders-order */
#[When(env: 'test')]
final class PackageControllerTest extends WebTestCase
{
    private const URL = '/admin/order/package/%s';

    private const ROLE = 'ROLE_ORDERS_STATUS';


    private static ?OrderUid $identifier;

    public static function setUpBeforeClass(): void
    {
        /** Инициируем статус */
        $new = new OrderStatusNew();

        // Получаем одно из событий Продукта
        $em = self::getContainer()->get(EntityManagerInterface::class);
        self::$identifier = $em->getRepository(Order::class)->findOneBy([], ['id' => 'DESC'])?->getId();
    }



    /** Доступ по без роли */
    public function testGuestFiled(): void
    {
        // Получаем одно из событий
        $identifier = self::$identifier;

        if ($identifier)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach (TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $client->request('GET', sprintf(self::URL, $identifier->getValue()));

                // Full authentication is required to access this resource
                self::assertResponseStatusCodeSame(401);
            }
        } else
        {
            self::assertTrue(true);
        }
    }

    /** Доступ по роли */
    public function testRoleSuccessful(): void
    {
        // Получаем одно из событий
        $identifier = self::$identifier;

        if ($identifier)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach (TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $user = TestUserAccount::getModer(self::ROLE);

                $client->loginUser($user, 'user');
                $client->request('GET', sprintf(self::URL, $identifier->getValue()));

                self::assertResponseIsSuccessful();
            }
        } else
        {
            self::assertTrue(true);
        }
    }

    // доступ по роли ROLE_ADMIN
    public function testRoleAdminSuccessful(): void
    {
        // Получаем одно из событий
        $identifier = self::$identifier;

        if ($identifier)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach (TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $user = TestUserAccount::getAdmin();

                $client->loginUser($user, 'user');
                $client->request('GET', sprintf(self::URL, $identifier->getValue()));

                self::assertResponseIsSuccessful();
            }
        } else
        {
            self::assertTrue(true);
        }
    }

    // доступ по роли ROLE_USER
    public function testRoleUserDeny(): void
    {
        // Получаем одно из событий
        $identifier = self::$identifier;

        if ($identifier)
        {
            self::ensureKernelShutdown();
            $client = static::createClient();

            foreach (TestUserAccount::getDevice() as $device)
            {
                $client->setServerParameter('HTTP_USER_AGENT', $device);

                $user = TestUserAccount::getUser();
                $client->loginUser($user, 'user');
                $client->request('GET', sprintf(self::URL, $identifier->getValue()));

                self::assertResponseStatusCodeSame(403);
            }
        } else
        {
            self::assertTrue(true);
        }
    }
}
