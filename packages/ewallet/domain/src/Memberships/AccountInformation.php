<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Memberships;

use Money\Money;

/**
 * This class enables access to a member's account balance
 */
class AccountInformation
{
    /** @var Money */
    private $balance;

    public function __construct(Money $balance)
    {
        $this->balance = $balance;
    }

    public function balance(): Money
    {
        return $this->balance;
    }
}
