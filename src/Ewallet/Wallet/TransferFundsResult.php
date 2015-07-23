<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Wallet;

use Ewallet\Accounts\Member;

class TransferFundsResult
{
    /** @var Member */
    private $fromMember;

    /** @var Member */
    private $toMember;

    /**
     * @param Member $fromMember
     * @param Member $toMember
     */
    function __construct(Member $fromMember, Member $toMember)
    {
        $this->fromMember = $fromMember;
        $this->toMember = $toMember;
    }

    /**
     * @return Member
     */
    public function fromMember()
    {
        return $this->fromMember;
    }

    /**
     * @return Member
     */
    public function toMember()
    {
        return $this->toMember;
    }
}