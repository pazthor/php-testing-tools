<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Memberships;

use Ewallet\ContractTests\Memberships\MembersTest;

class InMemoryMembersTest extends MembersTest
{
    /**
     * @return Members
     */
    protected function membersInstance(): Members
    {
        return new InMemoryMembers();
    }
}