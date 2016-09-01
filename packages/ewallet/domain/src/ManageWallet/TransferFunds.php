<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\ManageWallet;

use Ewallet\Memberships\Members;
use Hexagonal\DomainEvents\PublishesEvents;
use LogicException;

class TransferFunds
{
    use PublishesEvents;

    /** @var Members */
    private $members;

    /** @var CanTransferFunds */
    private $action;

    /**
     * @param Members $members
     */
    public function __construct(Members $members)
    {
        $this->members = $members;
    }

    /**
     * @param CanTransferFunds $action
     */
    public function attach(CanTransferFunds $action)
    {
        $this->action = $action;
    }

    /**
     * @param TransferFundsInformation $information
     */
    public function transfer(TransferFundsInformation $information)
    {
        $sender = $this->members->with($information->senderId());
        $recipient = $this->members->with($information->recipientId());

        $sender->transfer($information->amount(), $recipient);

        $this->members->update($sender);
        $this->members->update($recipient);

        $this->publisher()->publish($sender->events());

        $this->action()->transferCompleted(new TransferFundsSummary(
            $sender, $recipient
        ));
    }

    /**
     * @return CanTransferFunds
     * @throws LogicException
     */
    private function action(): CanTransferFunds
    {
        if ($this->action) {
            return $this->action;
        }
        throw new LogicException('No action was attached');
    }
}