<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\SymfonyConsole;

use Ewallet\Pimple\EwalletConsoleContainer;
use Ewallet\SymfonyConsole\Commands\TransferFundsCommand;
use Symfony\Component\Console\Application;

class EwalletApplication extends Application
{
    public function __construct(EwalletConsoleContainer $container)
    {
        parent::__construct('ewallet', '1.0.0');
        $this
            ->add(new TransferFundsCommand(
                $container['ewallet.transfer_funds_console_action'],
                $container['ewallet.transfer_filter_request']
            ))
        ;
        $this->setDispatcher($container['ewallet.console.dispatcher']);
    }
}
