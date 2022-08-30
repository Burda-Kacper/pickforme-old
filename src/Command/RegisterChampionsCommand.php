<?php

namespace App\Command;

use App\Entity\Champion;
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
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

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
            $championImageSrc = 'https://ddragon.leagueoflegends.com/cdn/img/champion/loading/' . $championImageName . '_0.jpg';
            $championImageSquareSrc = 'https://ddragon.leagueoflegends.com/cdn/12.16.1/img/champion/' . $championImageName . '.png';

            $championEntity = new Champion();
            $championEntity->setName($championName);
            $championEntity->setCodename($championImageName);
            $this->em->persist($championEntity);

            copy($championImageSrc, __DIR__ . "/../../public/img/champion/" . $championImageName . ".jpg");
            copy($championImageSquareSrc, __DIR__ . "/../../public/img/championSquare/" . $championImageName . ".jpg");
            echo "Done champion: " . $championName . " / " . $championImageName . "\n";
        }

        $this->em->flush();

        return Command::SUCCESS;
    }
}
