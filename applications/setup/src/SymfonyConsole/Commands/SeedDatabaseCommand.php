<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\SymfonyConsole\Commands;

use Ewallet\Accounts\Member;
use Hexagonal\DomainEvents\StoredEvent;
use Hexagonal\Messaging\PublishedMessage;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ewallet\TestHelpers\ProvidesDoctrineSetup;

class SeedDatabaseCommand extends Command
{
    use ProvidesDoctrineSetup;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('ewallet:db:seed')
            ->setDescription('Seed the database with some initial information')
        ;
    }

    /**
     * Seed some information to our database
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_setUpDoctrine(require __DIR__ . '/../../../config.php');
        $this->truncateTable(Member::class);
        $this->truncateTable(StoredEvent::class);
        $this->truncateTable(PublishedMessage::class);
        Fixtures::load(
            __DIR__ . '/../../../fixtures/members.yml',
            $this->entityManager
        );
    }

    /**
     * @param string $entity
     */
    private function truncateTable($entity)
    {
        $metadata = $this->entityManager->getClassMetadata($entity);
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $truncate = $platform->getTruncateTableSql($metadata->getTableName());
        $connection->executeUpdate($truncate);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
