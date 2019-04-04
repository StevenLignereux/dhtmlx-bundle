<?php

namespace Recode\DhtmlxBundle\Gantt;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractGantt implements GanttInterface
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var Request $request */
    protected $request;

    protected $ajax = array();
    protected $config = array();
    protected $mapping = array();
    protected $editing = true;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return AbstractGantt
     */
    public function setEntityManager(EntityManagerInterface $entityManager): AbstractGantt
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return AbstractGantt
     */
    public function setRequest(Request $request): AbstractGantt
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return array
     */
    public function getAjax(): array
    {
        return $this->ajax;
    }

    /**
     * @param array $ajax
     * @return AbstractGantt
     */
    public function setAjax(array $ajax): AbstractGantt
    {
        $this->ajax = $ajax;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return AbstractGantt
     */
    public function setConfig(array $config): AbstractGantt
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->mapping;
    }

    /**
     * @param array $mapping
     * @return AbstractGantt
     */
    public function setMapping(array $mapping): AbstractGantt
    {
        $this->mapping = $mapping;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEditing(): bool
    {
        return $this->editing;
    }

    /**
     * @param bool $editing
     * @return AbstractGantt
     */
    public function setEditing(bool $editing): AbstractGantt
    {
        $this->editing = $editing;
        return $this;
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function handleRequest(Request $request)
    {
        $this->request = $request;
        $this->editing = $request->query->get('editing');

        if ($this->editing) {
            $this->edit();
        }
    }

    /**
     * @throws \Exception
     */
    public function edit()
    {
        $data = $this->request->request->all();
        $id = $data['ids'];
        $repository = $this->entityManager->getRepository($this->getEntity());
        $accessor = PropertyAccess::createPropertyAccessor();
        $entity = $repository->find($id);
        echo $id;
        print_r($data);
        foreach ($this->mapping as $key => $field) {
            if ($key !== 'id') {
                if ($key == 'start_date') {
                    $value = new \DateTime($data[$id . '_' . $key]);
                } else {
                    $value = $data[$id . '_' . $key];
                }
                $accessor->setValue($entity, $this->mapping[$key], $value);
            }
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function getResponse()
    {
        $repository = $this->entityManager->getRepository($this->getEntity());
        $entities = $repository->findAll();
        $accessor = PropertyAccess::createPropertyAccessor();
        $data = [];

        foreach ($entities as $entity) {


            // On creer un tableau sans les parents et ajout du parent si il n'est pas null
            $child = [
                'id' => $accessor->getValue($entity, $this->mapping['id']),
                'text' => $accessor->getValue($entity, $this->mapping['text']),
                'start_date' => $accessor->getValue($entity, $this->mapping['start_date'])->format('d.m.Y'),
                'duration' => $accessor->getValue($entity, $this->mapping['duration']),
                'progress' => $accessor->getValue($entity, $this->mapping['progress']),
                'server' => true
            ];

            if ($entity->getParent()) {
                $child['parent'] = $accessor->getValue($entity, $this->mapping['parent'])->getId();
            }

            $data[] = $child;

        }

        return new JsonResponse([
            'data' => $data
        ]);
    }

    public function isSubmitted()
    {
        return $this->request->isXmlHttpRequest();
    }
}