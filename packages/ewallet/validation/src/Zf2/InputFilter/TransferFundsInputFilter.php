<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Zf2\InputFilter;

use Ewallet\Memberships\{MemberId, MembersRepository};
use Ewallet\ManageWallet\TransferFundsInput;
use Ewallet\Zf2\InputFilter\Filters\TransferFundsFilter;

class TransferFundsInputFilter implements TransferFundsInput
{
    /** @var TransferFundsFilter */
    private $filter;

    /** @var MembersRepository */
    private $members;

    /**
     * @param TransferFundsFilter $filter
     * @param MembersRepository $members
     */
    public function __construct(
        TransferFundsFilter $filter,
        MembersRepository $members
    ) {
        $this->filter = $filter;
        $this->members = $members;
    }

    /**
     * @param array $input
     */
    public function populate(array $input)
    {
        $this->filter->setData($input);
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $senderId = $this->filter->getRawValue('senderId');
        if ($senderId) {
            $recipients = $this->members->excluding(MemberId::with($senderId));
            $this->filter->configure($recipients);
        }

        return $this->filter->isValid();
    }

    /**
     * @return array
     */
    public function errorMessages(): array
    {
        return $this->filter->getMessages();
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return $this->filter->getValues();
    }
}
