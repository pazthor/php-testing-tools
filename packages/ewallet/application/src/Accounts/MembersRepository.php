<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Accounts;

interface MembersRepository extends Members
{
    /**
     * @param MemberId $memberId
     * @return Member[]
     */
    public function excluding(MemberId $memberId = null): array;
}
