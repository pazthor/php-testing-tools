<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Memberships;

use SplObjectStorage;

class InMemoryMembers implements Members
{
    /** @var SplObjectStorage */
    private $members;

    /**
     * Create an empty collection of members
     */
    public function __construct()
    {
        $this->members = new SplObjectStorage();
    }

    /**
     * @throws UnknownMember If a member with the given ID cannot be found
     */
    public function with(MemberId $memberId): Member
    {
        /** @var Member $member */
        foreach ($this->members as $member) {
            if ($member->information()->id()->equals($memberId)) {
                return $member;
            }
        }
        throw UnknownMember::identifiedBy($memberId);
    }

    public function add(Member $member)
    {
        $this->members->attach($member);
    }

    public function update(Member $member)
    {
        $this->members->attach($member);
    }
}
