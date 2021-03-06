<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Alice;

use Doctrine\Common\Persistence\ObjectManager;
use Ewallet\Memberships\Member;
use Nelmio\Alice\Fixtures;

/**
 * Fixture with 3 members, one with predefined information, 2 random. All of
 * them have a balance of $1000.00 MXN
 */
class ThreeMembersWithSameBalanceFixture
{
    /** @var ObjectManager */
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Cleanup before populating `members` table
     */
    public function load(): void
    {
        $this
            ->objectManager
            ->createQuery('DELETE FROM ' . Member::class)
            ->execute()
        ;
        Fixtures::load(
            __DIR__ . '/../../fixtures/members.yml',
            $this->objectManager
        );
    }
}
