<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Orders\Order\UseCase\Admin\Package\Invariable;

use BaksDev\Orders\Order\Entity\Invariable\OrderInvariableInterface;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\User\Type\Id\UserUid;
use DateTimeImmutable;
use ReflectionProperty;
use Symfony\Component\Validator\Constraints as Assert;

/** @see OrderInvariable */
final readonly class PackageOrderInvariableDTO implements OrderInvariableInterface
{
    /**
     * ID пользователя заказа
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private UserUid $usr;


    /**
     * ID профиля заказа
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private UserProfileUid $profile;


    /**
     * Usr
     */
    public function getUsr(): ?UserUid
    {
        if(!(new ReflectionProperty(self::class, 'usr'))->isInitialized($this))
        {
            return null;
        }

        return $this->usr;
    }

    public function setUsr(?UserUid $usr): self
    {
        if(is_null($usr))
        {
            return $this;
        }

        if(!(new ReflectionProperty(self::class, 'usr'))->isInitialized($this))
        {
            $this->usr = $usr;
        }

        return $this;
    }

    /**
     * Profile
     */
    public function getProfile(): ?UserProfileUid
    {
        if(!(new ReflectionProperty(self::class, 'profile'))->isInitialized($this))
        {
            return null;
        }

        return $this->profile;
    }

    public function setProfile(?UserProfileUid $profile): self
    {
        if(is_null($profile))
        {
            return $this;
        }

        if(!(new ReflectionProperty(self::class, 'profile'))->isInitialized($this))
        {
            $this->profile = $profile;
        }

        return $this;
    }
}
