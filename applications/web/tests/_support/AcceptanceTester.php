<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
use Codeception\Actor;
use _generated\AcceptanceTesterActions;
use Page\TransferFundsPage;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends Actor
{
    use AcceptanceTesterActions;

    public function amOnTransferFundsPage()
    {
        $this->amOnPage(TransferFundsPage::$inputTransferInformationPage);
    }

    /**
     * @param string $name Recipient's name
     * @param int $amount Amount in MXN
     */
    public function enterTransferInformation(string $name, int $amount)
    {
        $this->selectOption(TransferFundsPage::$recipients, $name);
        $this->fillField(TransferFundsPage::$amount, $amount);
    }

    public function makeTheTransfer()
    {
        $this->click(TransferFundsPage::$transferButton);
    }

    public function seeTransferCompletedConfirmation()
    {
        $this->seeCurrentUrlMatches('/' . TransferFundsPage::$transferCompletedPage . '/');
        $this->seeElement(TransferFundsPage::$successMessage);
    }
}
