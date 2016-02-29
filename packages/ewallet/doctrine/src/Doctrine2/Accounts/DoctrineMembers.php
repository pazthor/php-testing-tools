<?php
/**
 * PHP version 5.6
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace Ewallet\Doctrine2\Accounts;

use Doctrine\ORM\EntityRepository;
use Ewallet\Accounts\Member;
use Ewallet\Accounts\MemberId;
use Ewallet\Accounts\MembersRepository;
use Ewallet\Accounts\UnknownMember;

class DoctrineMembers extends EntityRepository implements MembersRepository
{
    /**
     * @param MemberId $id
     * @return Member
     * @throws UnknownMember
     */
    public function with(MemberId $id)
    {
        $builder = $this->createQueryBuilder('m');

        $builder
            ->where('m.memberId = :id')
            ->setParameter('id', $id)
        ;

        if (!$member = $builder->getQuery()->getOneOrNullResult()) {
            throw UnknownMember::with($id);
        }

        return $member;
    }

    /**
     * @param Member $member
     */
    public function add(Member $member)
    {
        $this->_em->persist($member);
        $this->_em->flush($member);
    }

    /**
     * @param Member $member
     */
    public function update(Member $member)
    {
        $this->_em->persist($member);
        $this->_em->flush($member);
    }

    /**
     * @param MemberId $memberId
     * @return Member[]
     */
    public function excluding(MemberId $memberId = null)
    {
        $builder = $this->createQueryBuilder('m');

        $builder
            ->where('m.memberId <> :id')
            ->setParameter('id', $memberId)
        ;

        return $builder->getQuery()->getResult();
    }
}