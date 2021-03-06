<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Hexagonal\Messaging;

use Exception;
use Hexagonal\DataBuilders\A;
use Hexagonal\DomainEvents\{InMemoryEventStore, StoredEvent};
use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;

class MessagePublisherTest extends TestCase
{
    /** @test */
    function it_publishes_a_single_message_to_an_empty_exchange()
    {
        $exchangeName = 'exchange_name';
        $aSingleMessage = [$message = A::storedEvent()->build()];
        $this->store->appendAll($aSingleMessage);

        $messages = $this->publisher->publishTo($exchangeName);

        $this->producer->send($exchangeName, $message)->shouldHaveBeenCalled();
        $this->assertEquals(
            1,
            $messages,
            'Should have processed only 1 message'
        );
    }

    /** @test */
    function it_publishes_several_messages_to_an_empty_exchange()
    {
        $exchangeName = 'exchange_name';
        $severalMessages = [
            A::storedEvent()->build(),
            A::storedEvent()->build(),
            A::storedEvent()->build(),
        ];
        $messagesCount = count($severalMessages);
        $this->store->appendAll($severalMessages);

        $messages = $this->publisher->publishTo($exchangeName);

        $this
            ->producer
            ->send($exchangeName, Argument::type(StoredEvent::class))
            ->shouldHaveBeenCalledTimes($messagesCount)
        ;
        $this->assertEquals(
            $messagesCount,
            $messages,
            'Should have processed 3 messages'
        );
    }

    /** @test */
    function it_publishes_a_single_message_to_a_non_empty_exchange()
    {
        $exchangeName = 'exchange_name';
        $mostRecentMessageId = 11000;
        $aSingleMessage = [
            $messageToPublish = A::storedEvent()->withId($mostRecentMessageId)->build(),
        ];
        $publishedMessage = A::publishedMessage()
            ->withExchangeName($exchangeName)
            ->build()
        ;
        $this->tracker->track($publishedMessage);
        $this->store->append(A::storedEvent()->from($publishedMessage)->build());
        $this->store->appendAll($aSingleMessage);

        $messages = $this->publisher->publishTo($exchangeName);

        $this->producer->send($exchangeName, $messageToPublish)->shouldHaveBeenCalled();
        $this->assertEquals(
            1,
            $messages,
            'Should have processed only 1 message'
        );
        $this->assertEquals(
            $mostRecentMessageId,
            $publishedMessage->mostRecentMessageId(),
            'Most recent message ID should be 11000'
        );
    }

    /** @test */
    function it_publishes_several_messages_to_a_non_empty_exchange()
    {
        $exchangeName = 'exchange_name';
        $mostRecentMessageId = 11000;
        $severalMessages = [
            A::storedEvent()->build(),
            A::storedEvent()->build(),
            A::storedEvent()->withId($mostRecentMessageId)->build(),
        ];
        $messagesCount = count($severalMessages);
        $publishedMessage = A::publishedMessage()
            ->withExchangeName($exchangeName)
            ->build()
        ;
        $this->store->append(A::storedEvent()->from($publishedMessage)->build());
        $this->store->appendAll($severalMessages);
        $this->tracker->track($publishedMessage);

        $messages = $this->publisher->publishTo($exchangeName);

        $this
            ->producer
            ->send($exchangeName, Argument::type(StoredEvent::class))
            ->shouldHaveBeenCalledTimes($messagesCount)
        ;
        $this->assertEquals(
            $messagesCount,
            $messages,
            "Should have processed $messagesCount messages"
        );
        $this->assertEquals(
            $mostRecentMessageId,
            $publishedMessage->mostRecentMessageId(),
            'Most recent message ID should be 11000'
        );
    }

    /** @test */
    function it_updates_last_published_message_when_publisher_fails_before_last_one()
    {
        $exchangeName = 'exchange_name';
        $mostRecentMessageId = 12000;
        $eventCausingException = A::storedEvent()->withId(11000)->build();
        $processedEvent = A::storedEvent()->withId($mostRecentMessageId)->build();
        $severalMessages = [
            $processedEvent,
            $eventCausingException,
            A::storedEvent()->build(),
        ];
        $message = A::publishedMessage()
            ->withExchangeName($exchangeName)
            ->build()
        ;
        $this->store->append(A::storedEvent()->from($message)->build());
        $this->tracker->track($message);
        $this->store->appendAll($severalMessages);
        $this->producer->open($exchangeName)->shouldBeCalled();
        $this
            ->producer
            ->send($exchangeName, $processedEvent)
            ->shouldBeCalled()
        ;
        $this
            ->producer
            ->send($exchangeName, $eventCausingException)
            ->willThrow(Exception::class)
        ;

        $messages = $this->publisher->publishTo($exchangeName);

        $this->assertEquals(
            1,
            $messages,
            'Should have processed only 1 message'
        );
        $this->assertEquals(
            $mostRecentMessageId,
            $message->mostRecentMessageId(),
            "Most recent message ID should be $mostRecentMessageId"
        );
    }

    /** @before */
    function configurePublisher()
    {
        $this->store = new InMemoryEventStore();
        $this->tracker = new InMemoryMessageTracker();
        $this->producer = $this->prophesize(MessageProducer::class);
        $this->publisher = new MessagePublisher(
            $this->store,
            $this->tracker,
            $this->producer->reveal()
        );
    }

    /** @var MessagePublisher */
    private $publisher;

    /** @var MessageProducer */
    private $producer;

    /** @var MessageTracker */
    private $tracker;

    /** @var InMemoryEventStore */
    private $store;
}
