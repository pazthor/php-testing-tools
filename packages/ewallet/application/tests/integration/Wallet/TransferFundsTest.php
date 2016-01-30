<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Wallet;

use Ewallet\Accounts\Member;
use Ewallet\PHPUnit\Constraints\ProvidesMoneyConstraints;
use Mockery;
use Nelmio\Alice\Fixtures;
use PHPUnit_Framework_TestCase as TestCase;
use Ewallet\TestHelpers\ProvidesDoctrineSetup;

class TransferFundsTest extends TestCase
{
    use ProvidesDoctrineSetup, ProvidesMoneyConstraints;

    public function setUp()
    {
        $this->_setUpDoctrine(require __DIR__ . '/../../../config.php');
        $this
            ->entityManager
            ->createQuery('DELETE FROM ' . Member::class)
            ->execute()
        ;
    }

    /** @test */
    function it_should_transfer_funds_between_members()
    {
        Fixtures::load(
            __DIR__ . '/../../fixtures/members.yml',
            $this->entityManager
        );

        /** @var Members $members */
        $members = $this->entityManager->getRepository(Member::class);
        $notifier = Mockery::mock(TransferFundsNotifier::class)->shouldIgnoreMissing();

        $transferBalance = new TransferFunds($members);
        $transferBalance->attach($notifier);

        $transferBalance->transfer($request = TransferFundsInformation::from([
            'fromMemberId' => 'XYZ',
            'toMemberId' => 'ABC',
            'amount' => 3,
        ]));

        $fromMember = $members->with($request->fromMemberId());
        $this->assertBalanceAmounts(700, $fromMember);

        $toMember = $members->with($request->toMemberId());
        $this->assertBalanceAmounts(1300, $toMember);
    }
}
