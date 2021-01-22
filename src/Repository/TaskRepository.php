<?php

namespace App\Repository;

use App\Constants\StatusConstants;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getTodoList($filterData = [], $onlyCount = false)
    {
        $filterQuery = $this->createQueryBuilder('todo');

        if ($onlyCount) {
            $filterQuery->select('count(todo.id)');
        } else {
            $filterQuery->select('todo.id, todo.name, todo.description, todo.status, todo.createdAt, todo.updatedAt');
        }

        if (!isset($filterData['status'])) {
            $filterQuery = $filterQuery->andWhere('todo.status NOT IN (' . StatusConstants::DELETED . ')');
        }

        foreach ($filterData as $filter => $data) {
            if (isset($data) && (!empty($data) || $data === "0")) {
                switch ($filter) {
                    case 'id':
                        $filterQuery = $filterQuery->andWhere('todo.id = :id')
                            ->setParameter('id', $data);
                        break;

                    case 'status':
                        $filterQuery = $filterQuery->andWhere('todo.status = :status')
                            ->setParameter('status', $data);
                        break;

                    case 'name':
                        $filterQuery = $filterQuery->andWhere('todo.name = :name')
                            ->setParameter('name', $data);
                        break;

                    case 'description':
                        $filterQuery = $filterQuery->andWhere('todo.description = :description')
                            ->setParameter('description', $data);
                        break;
                }
            }
        }

        if (isset($filterData['limit'])) {
            $filterQuery->setMaxResults($filterData['limit']);
        }

        if (isset($filterData['offset'])) {
            $filterQuery->setFirstResult($filterData['offset']);
        }

        if ($onlyCount) {
            return $filterQuery->getQuery()->getSingleScalarResult();
        }
        
        // Set default sorting order and field If not sent in the request
        $sortOrder = isset($filterData['sort']) ? strtoupper($filterData['sort']) : 'DESC';

        $sortField = 'todo.createdAt';

        if (isset($filterData['sortField'])) {
            // Set default and replace If its been sent over the request
            switch ($filterData['sortField']) {
                case 'id':
                    $sortField = 'todo.id';
                    break;
                case 'name':
                    $sortField = 'todo.name';
                    break;
                case 'description':
                    $sortField = 'todo.description';
                    break;
                case 'status':
                    $sortField = 'todo.id';
                    break;
            }
        }

        $filterQuery->orderBy($sortField, $sortOrder);

        return $filterQuery->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
