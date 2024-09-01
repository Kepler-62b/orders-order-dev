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

namespace BaksDev\Orders\Order\Messenger\ProductReserveByOrderNew;

use BaksDev\Products\Product\Type\Event\ProductEventUid;
use BaksDev\Products\Product\Type\Offers\Id\ProductOfferUid;
use BaksDev\Products\Product\Type\Offers\Variation\Id\ProductVariationUid;
use BaksDev\Products\Product\Type\Offers\Variation\Modification\Id\ProductModificationUid;

final class ProductReserveByOrderNewMessage
{
    private int $total;

    private ProductEventUid $event;

    private ?ProductOfferUid $offer;

    private ?ProductVariationUid $variation;

    private ?ProductModificationUid $modification;


    public function __construct(
        ProductEventUid $event,
        ?ProductOfferUid $offer,
        ?ProductVariationUid $variation,
        ?ProductModificationUid $modification,
        int $total,
    ) {
        $this->total = $total;
        $this->event = $event;
        $this->offer = $offer;
        $this->variation = $variation;
        $this->modification = $modification;
    }

    /**
     * Total
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Event
     */
    public function getEvent(): ProductEventUid
    {
        return $this->event;
    }

    /**
     * Offer
     */
    public function getOffer(): ?ProductOfferUid
    {
        return $this->offer;
    }

    /**
     * Variation
     */
    public function getVariation(): ?ProductVariationUid
    {
        return $this->variation;
    }

    /**
     * Modification
     */
    public function getModification(): ?ProductModificationUid
    {
        return $this->modification;
    }
}
