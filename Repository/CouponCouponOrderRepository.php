<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Plugin\Coupon\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * CouponCouponOrderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CouponCouponOrderRepository extends EntityRepository
{
    /**
     * クーポン受注情報を保存する
     *
     * @param \Plugin\Coupon\Entity\CouponCouponOrder $CouponCouponOrder
     */
    public function save(\Plugin\Coupon\Entity\CouponCouponOrder $CouponCouponOrder)
    {
        $em = $this->getEntityManager();
        $em->persist($CouponCouponOrder);
        $em->flush($CouponCouponOrder);

    }

    /**
     * 受注ID(order_id)から使用されたクーポン受注情報を取得する
     *
     * @param $orderId
     * @return mixed|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUseCouponByOrderId($orderId)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->andWhere('c.del_flg = 0')
            ->andWhere('c.order_date IS NOT NULL')
            ->andWhere('c.coupon_id IS NOT NULL')
            ->andWhere('c.order_id = :order_id')
            ->setParameter('order_id', $orderId);

        $query = $qb->getQuery();

        $result = null;
        try {
            $result = $query->getSingleResult();

        } catch (\Doctrine\Orm\NoResultException $e) {
            $result = null;

        }

        return $result;
    }

    /**
     * 会員または非会員が既にクーポンを利用しているか検索
     * 会員の場合、会員IDで非会員の場合、メールアドレスで検索
     *
     * @param $couponCd
     * @param $param
     * @return array
     */
    public function findUseCoupon($couponCd, $param)
    {

        $userId = null;
        $email = null;

        if (is_numeric($param)) {
            $userId = $param;
        } else {
            $email = $param;
        }

        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.coupon_cd = :coupon_cd')
            ->andWhere('c.order_date IS NOT NULL')
            ->andWhere('c.user_id = :user_id OR c.email = :email')
            ->setParameter('coupon_cd', $couponCd)
            ->setParameter('user_id', $userId)
            ->setParameter('email', $email);
        $query = $qb->getQuery();

        $result = $query->getResult();

        return $result;
    }

    /**
     * 会員または非会員が既にクーポンを利用しているか検索
     * 会員の場合、会員IDで非会員の場合、メールアドレスで検索
     *
     * @param $couponCd
     * @param $orderId
     * @param $param
     * @return array
     */
    public function findUseCouponBefore($couponCd, $orderId, $param)
    {

        $userId = null;
        $email = null;

        if (is_numeric($param)) {
            $userId = $param;
        } else {
            $email = $param;
        }

        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.coupon_cd = :coupon_cd')
            ->andWhere('c.order_id != :order_id')
            ->andWhere('c.user_id = :user_id OR c.email = :email')
            ->setParameter('coupon_cd', $couponCd)
            ->setParameter('order_id', $orderId)
            ->setParameter('user_id', $userId)
            ->setParameter('email', $email);
        $query = $qb->getQuery();

        $result = $query->getResult();

        return $result;
    }

    /**
     * クーポンの発行枚数を検索
     *
     * @param $couponCd
     * @return int|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countCouponByCd($couponCd)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('count(c.coupon_cd)')
            ->andWhere('c.del_flg = 0')
            ->andWhere('c.coupon_cd = :coupon_cd')
            ->andWhere('c.order_date IS NOT NULL')
            ->setParameter('coupon_cd', $couponCd);

        $query = $qb->getQuery();

        try {
            $count = $query->getSingleResult();

        } catch (\Doctrine\Orm\NoResultException $e) {
            $count = 0;

        }

        return $count;
    }


}
