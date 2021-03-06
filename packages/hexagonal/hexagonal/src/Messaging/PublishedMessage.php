<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Hexagonal\Messaging;

class PublishedMessage
{
    /** @var integer */
    private $id;

    /** @var string */
    private $exchangeName;

    /** @var int */
    private $mostRecentMessageId;

    public function __construct(string $exchangeName, int $mostRecentMessageId)
    {
        $this->exchangeName = $exchangeName;
        $this->mostRecentMessageId = $mostRecentMessageId;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function exchangeName(): string
    {
        return $this->exchangeName;
    }

    public function mostRecentMessageId(): int
    {
        return $this->mostRecentMessageId;
    }

    public function updateMostRecentMessageId(int $mostRecentMessageId)
    {
        $this->mostRecentMessageId = $mostRecentMessageId;
    }

    /**
     * 2 messages are equal if they have the same ID
     */
    public function equals(PublishedMessage $message): bool
    {
        return $this->id === $message->id;
    }
}
