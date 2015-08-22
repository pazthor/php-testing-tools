<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace specs\Ewallet\Accounts;

use Assert\InvalidArgumentException;
use PhpSpec\ObjectBehavior;

class EmailSpec extends ObjectBehavior
{
    function it_is_should_throw_exception_if_invalid_email_is_provided()
    {
        $this->beConstructedWith('invalid email address');
        try {
            $this->getWrappedObject();
            throw new ExampleException('Expected exception was not thrown');
        }
        catch(InvalidArgumentException $e) {}
    }

    function it_should_be_casted_to_string()
    {
        $this->beConstructedWith('montealegreluis@gmail.com');
        $this->__toString()->shouldBe('montealegreluis@gmail.com');
    }
}