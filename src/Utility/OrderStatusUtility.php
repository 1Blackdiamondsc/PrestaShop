<?php
/**
 * Copyright (c) 2012-2020, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @author     Mollie B.V. <info@mollie.nl>
 * @copyright  Mollie B.V.
 * @license    Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @category   Mollie
 * @package    Mollie
 * @link       https://www.mollie.nl
 * @codingStandardsIgnoreStart
 */

namespace Mollie\Utility;

use _PhpScoper5eddef0da618a\Mollie\Api\Resources\Order as MollieOrderAlias;
use _PhpScoper5eddef0da618a\Mollie\Api\Resources\Payment as MolliePaymentAlias;
use _PhpScoper5eddef0da618a\Mollie\Api\Types\PaymentStatus;
use _PhpScoper5eddef0da618a\Mollie\Api\Types\RefundStatus;
use Mollie\Config\Config;


class OrderStatusUtility
{
    /**
     * @param string $status
     * @param string $comparedStatus
     * @return string
     */
    public static function transformPaymentStatusToPaid($status, $comparedStatus)
    {
        if($status === $comparedStatus) {
            return PaymentStatus::STATUS_PAID;
        }

        return $status;
    }

    /**
     * @param MolliePaymentAlias|MollieOrderAlias $transaction
     */
    public static function transformPaymentStatusToRefunded($transaction)
    {
        if ($transaction->amountRefunded === null ||
            $transaction->amountCaptured === null) {
            return $transaction->status;
        }

        $amountRefunded = $transaction->amountRefunded->value;
        $amountPayed = $transaction->amountCaptured->value;
        $isPartiallyRefunded = NumberUtility::isLowerThan($amountRefunded, $amountPayed);
        $isFullyRefunded = NumberUtility::isEqual($amountRefunded, $amountPayed);

        if ($isPartiallyRefunded) {
            return Config::PARTIAL_REFUND_CODE;
        } elseif ($isFullyRefunded) {
            return RefundStatus::STATUS_REFUNDED;
        }

        return $transaction->status;
    }
}
