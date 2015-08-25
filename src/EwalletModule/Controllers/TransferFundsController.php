<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace EwalletModule\Controllers;

use Ewallet\Accounts\Identifier;
use Ewallet\Wallet\TransferFunds;
use Ewallet\Wallet\TransferFundsNotifier;
use Ewallet\Wallet\TransferFundsRequest;
use Ewallet\Wallet\TransferFundsResponse;
use EwalletModule\Bridges\EasyForms\TransferFundsFormResponder;

class TransferFundsController implements TransferFundsNotifier
{
    /** @var TransferFunds */
    private $useCase;

    /** @var TransferFundsFormResponder */
    private $responder;

    /**
     * @param TransferFundsFormResponder $responder
     * @param TransferFunds $transferFunds
     */
    public function __construct(
        TransferFundsFormResponder $responder,
        TransferFunds $transferFunds = null
    ) {
        $this->responder = $responder;
        $this->useCase = $transferFunds;
        $transferFunds && $this->useCase->attach($this);
    }

    /**
     * @param Identifier $fromMemberId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showForm(Identifier $fromMemberId)
    {
        $this->responder->transferFundsFormResponse($fromMemberId);

        return $this->responder->response();
    }

    /**
     * @param FilteredRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function transfer(FilteredRequest $request)
    {
        if (!$request->isValid()) {
            $this->validationFailedFor($request);
        } else {
            $this->useCase->transfer(TransferFundsRequest::from($request->values()));
        }

        return $this->responder->response();
    }

    /**
     * @param FilteredRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function validationFailedFor(FilteredRequest $request)
    {
        $this->responder->invalidTransferInputResponse(
            $request->errorMessages(),
            $request->values(),
            $request->value('fromMemberId')
        );
    }

    /**
     * @param TransferFundsResponse $response
     */
    public function transferCompleted(TransferFundsResponse $response)
    {
        $this->responder->transferCompletedResponse($response);
    }
}
