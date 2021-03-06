<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\PHPUnit\Constraints;

use Money\Money;
use PHPUnit_Framework_Constraint as Constraint;

class ExactAmountConstraint extends Constraint
{
    /** @var int */
    private $expectedAmount;

    public function __construct(int $expected)
    {
        $this->expectedAmount = $expected;
        parent::__construct();
    }

    /**
     * Returns true only if the amount of provided `Money` object is equal to
     * the expected one
     *
     * @param mixed $other
     * @return boolean
     */
    protected function matches($other)
    {
        /** @var Money $other */
        return $this->expectedAmount == $other->getAmount();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return "has the correct amount, expecting {$this->expectedAmount}";
    }
}
