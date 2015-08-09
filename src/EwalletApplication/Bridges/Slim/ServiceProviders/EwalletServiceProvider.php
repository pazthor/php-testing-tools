<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace EwalletApplication\Bridges\Slim\ServiceProviders;

use ComPHPPuebla\Slim\Resolver;
use ComPHPPuebla\Slim\ServiceProvider;
use Ewallet\Accounts\Member;
use Ewallet\Bridges\Hexagonal\Wallet\TransferFundsTransactionally;
use EwalletApplication\Bridges\Slim\Controllers\SlimController;
use EwalletModule\Bridges\Twig\Extensions\EwalletExtension;
use EwalletModule\Bridges\Zf2InputFilter\Filters\TransferFundsFilter;
use EwalletModule\Bridges\Zf2InputFilter\TransferFundsInputFilterRequest;
use EwalletModule\Controllers\TransferFundsController;
use EwalletModule\Controllers\TransferFundsResponder;
use EwalletModule\Forms\MembersConfiguration;
use EwalletModule\Forms\TransferFundsForm;
use Hexagonal\Application\Services\TransactionalService;
use Hexagonal\Bridges\Doctrine2\Application\Services\DoctrineSession;
use Slim\Slim;
use Twig_Environment as Environment;
use Twig_Loader_Filesystem as Loader;

class EwalletServiceProvider implements ServiceProvider
{
    /**
     * @param Slim $app
     * @param Resolver $resolver
     * @param array $options
     */
    public function configure(Slim $app, Resolver $resolver, array $options = [])
    {
        $app->container->singleton(
            'ewallet.member_repository',
            function () use ($app) {
                return $app
                    ->container
                    ->get('doctrine.em')
                    ->getRepository(Member::class)
                ;
            }
        );
        $app->container->singleton(
            'ewallet.transfer_form',
            function () {
                return new TransferFundsForm();
            }
        );
        $app->container->singleton(
            'ewallet.transfer_filter_request',
            function () use ($app) {
                return new TransferFundsInputFilterRequest(
                    new TransferFundsFilter(),
                    $app->container->get('ewallet.members_configuration'),
                    $app->request()->post()
                );
            }
        );
        $app->container->singleton(
            'ewallet.members_configuration',
            function () use ($app) {
                return new MembersConfiguration(
                    $app->container->get('ewallet.member_repository')
                );
            }
        );
        $app->container->singleton(
            'ewallet.transfer_funds_responder',
            function () use ($app) {
                return new TransferFundsResponder(
                    $app->container->get('twig.environment'),
                    $app->container->get('ewallet.transfer_form'),
                    $app->container->get('ewallet.members_configuration')
                );
            }
        );
        $app->container->singleton(
            'ewallet.transfer_form_controller',
            function () use ($app) {
                return new SlimController(new TransferFundsController(
                    $app->container->get('ewallet.transfer_funds_responder')
                ));
            }
        );
        $app->container->singleton(
            'ewallet.transfer_funds',
            function () use ($app) {
                $transferFunds = new TransferFundsTransactionally(
                    $app->container->get('ewallet.member_repository')
                );
                $transferFunds->setTransactionalSession(new DoctrineSession(
                    $app->container->get('doctrine.em')
                ));

                return $transferFunds;
            }
        );
        $app->container->singleton(
            'ewallet.transfer_funds_controller',
            function () use ($app) {
                return new SlimController(new TransferFundsController(
                    $app->container->get('ewallet.transfer_funds_responder'),
                    $app->container->get('ewallet.transfer_funds')
                ));
            }
        );
        $app->container->singleton(
            'ewallet.twig.extension',
            function () {
                return new EwalletExtension();
            }
        );
        $resolver->extend(
            $app,
            'twig.loader',
            function (Loader $loader) {
                $loader->addPath(__DIR__ . '/../Resources/templates');
                $loader->addPath(
                    __DIR__ . '/../../../../EwalletModule/Bridges/Twig/Resources/views'
                );

                return $loader;
            }
        );
        $resolver->extend(
            $app,
            'twig.environment',
            function (Environment $environment) use ($app) {
                $environment->addExtension(
                    $app->container->get('ewallet.twig.extension')
                );

                return $environment;
            }
        );
    }
}