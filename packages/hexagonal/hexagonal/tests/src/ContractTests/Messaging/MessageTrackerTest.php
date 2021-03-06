<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Hexagonal\ContractTests\Messaging;

use Hexagonal\DataBuilders\A;
use Hexagonal\Messaging\MessageTracker;
use Hexagonal\Messaging\PublishedMessage;
use PHPUnit_Framework_TestCase as TestCase;

abstract class MessageTrackerTest extends TestCase
{
    /** @test */
    function it_recognize_empty_exchanges()
    {
        $this->assertFalse($this->tracker->hasPublishedMessages('empty_exchange'));
    }

    /** @test */
    function it_recognizes_non_empty_exchanges()
    {
        $message = new PublishedMessage('non_empty_exchange', $arbitraryId = 1);
        $this->tracker->track($message);

        $this->assertTrue($this->tracker->hasPublishedMessages('non_empty_exchange'));
    }

    /**
     * @test
     * @expectedException \Hexagonal\Messaging\EmptyExchange
     */
    function it_throws_exception_when_trying_to_get_the_last_message_from_an_empty_exchange()
    {
        $this->tracker->mostRecentPublishedMessage('non_empty_exchange');
    }

    /** @test */
    function it_returns_last_published_message_in_a_given_exchange()
    {
        $message = new PublishedMessage('not_empty_exchange', $arbitraryId = 1);
        $this->tracker->track($message);
        $message->updateMostRecentMessageId($arbitraryId = 2);
        $this->tracker->track($message);
        $message->updateMostRecentMessageId($arbitraryId = 3);
        $this->tracker->track($message);
        $message->updateMostRecentMessageId(static::LAST_PUBLISHED_MESSAGE_ID);
        $this->tracker->track($message);

        $message = $this->tracker->mostRecentPublishedMessage('not_empty_exchange');

        $this->assertEquals(
            static::LAST_PUBLISHED_MESSAGE_ID,
            $message->mostRecentMessageId()
        );
    }

    /**
     * @test
     * @expectedException \Hexagonal\Messaging\InvalidPublishedMessageToTrack
     */
    function it_does_not_allow_more_than_one_last_message_for_each_exchange()
    {
        $originalId = 1;
        $aDifferentId = 2;
        $exchangeName = 'non_empty_exchange';
        $this->tracker->track(
            A::publishedMessage()
                ->withExchangeName($exchangeName)
                ->withId($originalId)
                ->build()
        );
        $aDifferentMessage = A::publishedMessage()
            ->withExchangeName($exchangeName)
            ->withId($aDifferentId)
            ->build()
        ;

        $this->tracker->track($aDifferentMessage);
    }

    /** @before */
    function createTracker()
    {
        $this->tracker = $this->messageTracker();
    }

    abstract function messageTracker(): MessageTracker;

    /** @var MessageTracker */
    protected $tracker;

    protected const LAST_PUBLISHED_MESSAGE_ID = 5;
}
