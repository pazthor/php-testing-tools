<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\ManageWallet;

use Ewallet\Memberships\MemberId;
use Ewallet\EasyForms\{MembersConfiguration, TransferFundsForm};
use Ewallet\ManageWallet\Web\{ResponseFactory, TransferFundsWebResponder};
use Ewallet\Templating\TemplateEngine;
use Psr\Http\Message\ResponseInterface;

class TransferFundsFormResponder implements TransferFundsWebResponder
{
    /** @var ResponseFactory */
    private $factory;

    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

    /** @var TemplateEngine */
    private $template;

    /** @var TransferFundsForm */
    private $form;

    /** @var MembersConfiguration */
    private $configuration;

    public function __construct(
        TemplateEngine $template,
        ResponseFactory $factory,
        TransferFundsForm $form,
        MembersConfiguration $configuration
    ) {
        $this->template = $template;
        $this->factory = $factory;
        $this->form = $form;
        $this->configuration = $configuration;
    }

    public function respondToTransferCompleted(TransferFundsSummary $summary)
    {
        $this->form->configure($this->configuration, $summary->sender()->id());

        $html = $this->template->render('member/transfer-funds.html', [
            'form' => $this->form->buildView(),
            'sender' => $summary->sender(),
            'recipient' => $summary->recipient(),
        ]);

        $this->response = $this->factory->buildResponse($html);
    }

    /**
     * @param string[] $messages
     * @param string[] $values
     */
    public function respondToInvalidTransferInput(
        array $messages,
        array $values
    ) {
        $this->form->submit($values);
        $this->form->setErrorMessages($messages);

        $this->respondToEnterTransferInformation(MemberId::withIdentity($values['senderId']));
    }

    public function respondToEnterTransferInformation(MemberId $senderId)
    {
        $this->form->configure($this->configuration, $senderId);

        $html = $this->template->render('member/transfer-funds.html', [
            'form' => $this->form->buildView(),
        ]);

        $this->response = $this->factory->buildResponse($html);
    }

    public function response(): ResponseInterface
    {
        return $this->response;
    }
}
