<?php
/**
 * PHP version 7.0
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\PhpSpec\Matchers;

use Money\Money;
use PhpSpec\Exception\Example\FailureException;

/**
 * Adds `shouldAmount` inline matcher to verify that an amount expressed in
 * cents is equal to the amount of the subject `Money` object
 */
trait ProvidesMoneyMatcher
{
    /**
     * @return array
     */
    public function getMatchers()
    {
        return [
            'amount' => [$this, 'amountMatches'],
        ];
    }

    /**
     * @throws FailureException If the given and expected amounts are different
     */
    public function amountMatches(Money $subject, string $expectedAmount): bool
    {
        if ($subject->getAmount() !== $expectedAmount) {
            throw new FailureException(sprintf(
                'Current money amount should be "%s" not "%s".',
                $expectedAmount,
                $subject->getAmount()
            ));
        }
        return true;
    }
}
