<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Pimple\ServiceProviders;

use Ewallet\Memberships\{Member, MemberFormatter};
use Ewallet\Doctrine2\Application\Services\DoctrineSession;
use Ewallet\EasyForms\MembersConfiguration;
use Ewallet\Twig\{Extensions\EwalletExtension, TwigTemplateEngine};
use Ewallet\ManageWallet\{TransferWasMadeLogger, TransferFundsTransactionally};
use Ewallet\Zf2\InputFilter\{Filters\TransferFundsFilter, TransferFundsInputFilter};
use Hexagonal\JmsSerializer\JsonSerializer;
use Hexagonal\DomainEvents\{
    EventPublisher,
    PersistEventsSubscriber,
    StoredEvent,
    StoredEventFactory
};
use Monolog\{Handler\SyslogHandler, Logger};
use Pimple\{Container, ServiceProviderInterface};

class EwalletServiceProvider implements ServiceProviderInterface
{
    /**
     * Register the services for Transfer Funds feature delivered through a
     * console command
     */
    public function register(Container $container)
    {
        $container['ewallet.member_repository'] = function () use ($container) {
            return $container['doctrine.em']->getRepository(Member::class);
        };
        $container['ewallet.members_configuration'] = function () use ($container) {
            return new MembersConfiguration(
                $container['ewallet.member_repository']
            );
        };
        $container['ewallet.transfer_filter_request'] = function () use ($container) {
            return new TransferFundsInputFilter(
                new TransferFundsFilter(),
                $container['ewallet.member_repository']
            );
        };
        $container['ewallet.template_engine'] = function () use ($container) {
            return new TwigTemplateEngine($container['twig.environment']);
        };
        $container['ewallet.transfer_funds'] =  function () use ($container) {
            $transferFunds = new TransferFundsTransactionally(
                $container['ewallet.member_repository']
            );
            $transferFunds->setTransactionalSession(new DoctrineSession(
                $container['doctrine.em']
            ));
            $transferFunds->setPublisher($container['ewallet.events_publisher']);

            return $transferFunds;
        };
        $container['ewallet.member_formatter'] = function () {
            return new MemberFormatter();
        };
        $container['ewallet.events_publisher'] = function () use ($container) {
            $publisher = new EventPublisher();
            $publisher->subscribe($container['ewallet.transfer_funds_logger']);

            return $publisher;
        };
        $container['ewallet.event_store'] = function () use ($container) {
            return $container['doctrine.em']->getRepository(StoredEvent::class);
        };
        $container['ewallet.event_persist_subscriber'] = function () use ($container) {
            return new PersistEventsSubscriber(
                $container['ewallet.event_store'],
                new StoredEventFactory(new JsonSerializer())
            );
        };
        $container['ewallet.transfer_funds_logger'] = function () use ($container) {
            return new TransferWasMadeLogger(
                $container['ewallet.logger'], $container['ewallet.member_formatter']
            );
        };
        $container['ewallet.logger'] = function () use ($container) {
            $logger = new Logger($container['monolog']['ewallet']['channel']);
            $logger->pushHandler(new SyslogHandler(
                $container['monolog']['ewallet']['channel'],
                LOG_USER,
                Logger::DEBUG
            ));

            return $logger;
        };
        $container['ewallet.twig.extension'] = function () {
            return new EwalletExtension(new MemberFormatter());
        };
    }
}
