<?php

namespace Recode\DhtmlxBundle\Gantt;

interface GanttInterface
{
    public function configure();

    public function getEntity();

    public function getName();
}