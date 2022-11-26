<?php

namespace App\Service;

use App\Entity\PFMLog;
use Doctrine\ORM\EntityManagerInterface;

class PFMLogService
{
    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $mode
     * @param int $result
     * @param string $extra
     *
     * @return void
     */
    public function createLog(string $mode, int $result, string $extra = ""): void
    {
        $PFMLog = new PFMLog();
        $PFMLog->setMode($mode);
        $PFMLog->setResult($result);
        $PFMLog->setExtra($extra);

        $this->em->persist($PFMLog);
        $this->em->flush();
    }
}