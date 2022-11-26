<?php

namespace App\Service;

class ChooseService
{
    /**
     * @param array $champions
     * @param array $tags
     *
     * @return array
     */
    public function pickChooseChampion(array $champions, array $tags): array
    {
        $outputChampions = [];

        foreach ($champions as $champion) {
            $championScore = 0;

            foreach ($champion['tagIds'] as $tagId) {
                if (in_array($tagId, $tags)) {
                    $championScore++;
                }
            }

            $outputChampions[$championScore][] = $champion;
        }

        krsort($outputChampions);
        $possibleChampions = $outputChampions[key($outputChampions)];

        shuffle($possibleChampions);
        shuffle($possibleChampions);
        shuffle($possibleChampions);

        return $possibleChampions[0];
    }
}