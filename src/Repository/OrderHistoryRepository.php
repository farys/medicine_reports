<?php

namespace App\Repository;

use App\Entity\OrderHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderHistory>
 */
class OrderHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderHistory::class);
    }

    public function getTotalCount() : int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopSellingMedicines(int $limit = 30) : array
    {
        return $this->createQueryBuilder('o')
            ->select('o.product, COUNT(1) AS salesCount')
            ->groupBy('o.product')
            ->orderBy('salesCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getTopCountriesInGroup() : array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT `t2`.`customer_group`, GROUP_CONCAT(`country`) as `countries` 
            FROM (select `customer_group`, `country`, count(*) as `counter`, 
            rank() over (partition by `customer_group` order by count(*) desc) as `rank` 
            from `order_history` group by `customer_group`, `country`) t2 
            WHERE `rank`=1 GROUP BY `t2`.`customer_group`;
            ';

        return $conn->executeQuery($sql)
            ->fetchAllAssociative();
    }

    public function getTopSourcesInCustomerStatus() : array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT `customer_status`, GROUP_CONCAT(`source`) as `sources` 
            FROM (SELECT `customer_status`, `source`, count(1) as `counter`, 
            rank() over (partition by `customer_status` order by count(1) desc) as `rank`  
            FROM `order_history` GROUP BY `customer_status`, `source` order by `customer_status`) `t2`
            WHERE `t2`.`rank` = 1 GROUP BY `customer_status`;
            ';

        return $conn->executeQuery($sql)
            ->fetchAllAssociative();
    }

    public function getTotalConsonants()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        select sum(`consonants`) as `totalConsonants` FROM (SELECT `customer`, CHAR_LENGTH(REGEXP_REPLACE(`customer`, \'[aeiouyAEIOUY]+\', \'\'))-1 as `consonants` 
        FROM `order_history`) t2;
        ';

        return $conn->executeQuery($sql)->fetchAllAssociative()[0]['totalConsonants'];
    }
}
