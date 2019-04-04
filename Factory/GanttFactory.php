<?php

namespace Recode\DhtmlxBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Recode\DhtmlxBundle\Gantt\AbstractGantt;

class GanttFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * GanttFactory constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $className
     */
    public function create($className)
    {
        /** @var AbstractGantt $gantt */
        $gantt = new $className;
        $gantt
            ->setEntityManager($this->entityManager)
            ->configure();

        return $gantt;
    }
}
