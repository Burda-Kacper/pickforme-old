<?php

namespace App\Service;

use App\Entity\Champion;
use App\Entity\Tag;
use App\Repository\ChampionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChampionService
{
    /**
     * @var ChampionRepository $championRepo
     */
    private ChampionRepository $championRepo;

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @param ChampionRepository $championRepo
     * @param EntityManagerInterface $em
     */
    public function __construct(ChampionRepository $championRepo, EntityManagerInterface $em)
    {
        $this->championRepo = $championRepo;
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getAllChampions(): array
    {
        $champions = $this->championRepo->findBy([], [
            'position' => "ASC"
        ]);

        $championsOutput = [];

        foreach ($champions as $champion) {
            $championsOutput[] = [
                'id' => $champion->getId(),
                'codename' => $champion->getCodename(),
                'name' => $champion->getName(),
                'tagIds' => array_map(function (Tag $tag) {
                    return $tag->getId();
                }, $champion->getTags()->toArray())
            ];
        }

        return $championsOutput;
    }

    /**
     * @param int $championId
     *
     * @return Champion|null
     */
    public function getChampionDetails(int $championId): ?Champion
    {
        return $this->championRepo->findOneBy([
            'id' => $championId
        ]);
    }

    /**
     * @param int $championId
     * @param array $data
     *
     * @return array
     */
    public function saveChampionDetails(int $championId, array $data): array
    {
        $champion = $this->getChampionDetails($championId);

        if (!$champion) {
            return [
                'success' => false,
                'message' => 'administrator.champions.not-found'
            ];
        }

        if (isset($data['tags']) && isset($data['allTags'])) {
            foreach ($data['allTags'] as $tag) {
                $champion->removeTag($tag);
            }

            foreach ($data['tags'] as $tag) {
                $champion->addTag($tag);
            }
        }

        $this->em->persist($champion);
        $this->em->flush();

        return [
            'success' => true,
            'message' => 'administrator.champions.success'
        ];
    }
}