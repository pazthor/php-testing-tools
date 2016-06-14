<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Accounts;

use Money\Money;
use RuntimeException;

/**
 * This exception is thrown to prevent a member to overdraw her account
 */
class InsufficientFunds extends RuntimeException
{
    /**
     * @param Money $amount
     * @return InsufficientFunds
     */
    public static function withdrawing(Money $amount)
    {
        return new self("Cannot withdraw {$amount->getAmount()}");
    }
}
