<?php

namespace App\Service;

use App\Entity\Champion;
use App\Repository\ChampionRepository;

class ChampionService
{
    /**
     * @var ChampionRepository $championRepo
     */
    private ChampionRepository $championRepo;

    /**
     * @param ChampionRepository $championRepo
     */
    public function __construct(ChampionRepository $championRepo)
    {
        $this->championRepo = $championRepo;
    }

    /**
     * @return array
     */
    public function getAllChampions(): array
    {
        $champions = $this->championRepo->findAll();
        $championsOutput = [];

        foreach ($champions as $champion) {
            $championsOutput[] = [
                'codename' => $champion->getCodename(),
                'name' => $champion->getName()
            ];
        }

        return $championsOutput;
    }
}