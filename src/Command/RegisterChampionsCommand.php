<?php

namespace App\Command;

use App\Entity\Champion;
use App\Repository\ChampionRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterChampionsCommand extends Command
{
    protected static $defaultName = 'pfm:champions:register';

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var ChampionRepository $championRepository
     */
    private ChampionRepository $championRepository;

    /**
     * @var TagRepository $tagRepository
     */
    private TagRepository $tagRepository;

    /**
     * @param EntityManagerInterface $em
     * @param ChampionRepository $championRepository
     * @param TagRepository $tagRepository
     */
    public function __construct(
        EntityManagerInterface $em,
        ChampionRepository     $championRepository,
        TagRepository          $tagRepository
    )
    {
        $this->em = $em;
        $this->championRepository = $championRepository;
        $this->tagRepository = $tagRepository;

        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $championData = json_decode(file_get_contents('https://ddragon.leagueoflegends.com/cdn/12.16.1/data/en_US/champion.json'));

        foreach ($championData->data as $champion) {
            $championName = $champion->name;
            $championImageName = $champion->id;

            $this->fetchChampionLoadingImage($championImageName);
            $this->fetchChampionSquareImage($championImageName);
            $this->saveChampion([
                'championName' => $championName,
                'championImageName' => $championImageName,
                'resource' => $this->convertResource($champion->partype),
                'tags' => [],
                'attackRange' => $champion->stats->attackrange
            ]);
        }

        $this->em->flush();

        return Command::SUCCESS;
    }

    /**
     * @param string $championImageName
     *
     * @return void
     */
    private function fetchChampionLoadingImage(string $championImageName): void
    {
        $championLoadingImageSrc = __DIR__ . "/../../public/img/champion/" . $championImageName . ".jpg";

        if (file_exists($championLoadingImageSrc)) {
            echo "\n[SKIPPING] Already had loading image for: " . $championImageName;

            return;
        }

        echo "\n[DOWNLOADING] Downloading loading image for: " . $championImageName;
        $championImageSrc = 'https://ddragon.leagueoflegends.com/cdn/img/champion/loading/' . $championImageName . '_0.jpg';
        copy($championImageSrc, $championLoadingImageSrc);
    }

    /**
     * @param string $championImageName
     *
     * @return void
     */
    private function fetchChampionSquareImage(string $championImageName): void
    {
        $championSquareImageSrc = __DIR__ . "/../../public/img/championSquare/" . $championImageName . ".jpg";

        if (file_exists($championSquareImageSrc)) {
            echo "\n[SKIPPING] Already had square image for: " . $championImageName;

            return;
        }

        echo "\n[DOWNLOADING] Downloading square image for: " . $championImageName;
        $championImageSquareSrc = 'https://ddragon.leagueoflegends.com/cdn/12.16.1/img/champion/' . $championImageName . '.png';
        copy($championImageSquareSrc, $championSquareImageSrc);
    }

    /**
     * @param array $championData
     *
     * @return void
     */
    private function saveChampion(array $championData): void
    {
        $championEntity = $this->championRepository->findOneBy([
            'codename' => $championData['championImageName']
        ]);

        if (!$championEntity) {
            echo "\n[CREATING] A new champion has to be created: " . $championData['championImageName'];
            $championEntity = new Champion();
        } else {
            echo "\n[UPDATING] Champion data is being updated: " . $championData['championImageName'];
        }

        $championEntity->setName($championData['championName']);
        $championEntity->setCodename($championData['championImageName']);
        $championEntity->setResource($championData['resource']);
        $championEntity->setAttackRange($championData['attackRange']);

        foreach ($championData['tags'] as $tag) {
            $championEntity->addTag($tag);
        }

        $this->em->persist($championEntity);
    }

    /**
     * @param string $resource
     *
     * @return string
     */
    private function convertResource(string $resource): string
    {
        if ($resource === "Mana") {
            return "Mana";
        }

        if ($resource === "Energy") {
            return "Energy";
        }

        return "Other";
    }
}
