<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Zf2\InputFilter;

use Ewallet\Accounts\MemberId;
use Ewallet\Accounts\MembersRepository;
use Ewallet\Zf2\InputFilter\Filters\TransferFundsFilter;
use Ewallet\Actions\TransferFundsRequest;

class TransferFundsInputFilterRequest implements TransferFundsRequest
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
        if (isset($input['fromMemberId']) && !empty($input['fromMemberId'])) {
            $membersToTransferTo = $this->members->excluding(
                MemberId::with($input['fromMemberId'])
            );
        } else {
            $membersToTransferTo = $this->members->excluding();
        }
        $this->filter->configure($membersToTransferTo);
        $this->filter->setData($input);
    }

    /**
     * @return boolean
     */
    public function isValid()
    {
        return $this->filter->isValid();
    }

    /**
     * @return array
     */
    public function errorMessages()
    {
        return $this->filter->getMessages();
    }

    /**
     * @return array
     */
    public function values()
    {
        return $this->filter->getValues();
    }
}
