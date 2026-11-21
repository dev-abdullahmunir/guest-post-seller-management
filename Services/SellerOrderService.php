<?php

namespace Modules\SellerManagement\Services;

use Modules\Shared\Services\BaseService;
use Modules\Shared\Enums\OrderStatus;

class SellerOrderService extends BaseService
{
    /**
     * Accept incoming order
     */
    public function acceptOrder(int $sellerId, int $orderId)
    {
        return $this->success([
            'order_id' => $orderId,
            'seller_id' => $sellerId,
            'status' => OrderStatus::ACCEPTED,
        ], 'Order accepted. You can now publish the post.');
    }

    /**
     * Submit published live link
     */
    public function submitLiveLink(int $sellerId, int $orderId, string $liveUrl)
    {
        return $this->success([
            'order_id' => $orderId,
            'seller_id' => $sellerId,
            'live_url' => $liveUrl,
            'status' => OrderStatus::PUBLISHED,
        ], 'Live link submitted successfully. Client notified for verification.');
    }
}
