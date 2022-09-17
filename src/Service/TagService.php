<?php

namespace App\Service;

use App\Repository\TagGroupRepository;
use App\Repository\TagRepository;

class TagService
{
    /**
     * @var TagRepository $tagRepo
     */
    private TagRepository $tagRepo;

    /**
     * @var TagGroupRepository $tagGroupRepo
     */
    private TagGroupRepository $tagGroupRepo;

    /**
     * @param TagRepository $tagRepo
     * @param TagGroupRepository $tagGroupRepo
     */
    public function __construct(TagRepository $tagRepo, TagGroupRepository $tagGroupRepo)
    {
        $this->tagRepo = $tagRepo;
        $this->tagGroupRepo = $tagGroupRepo;
    }

    /**
     * @return array
     */
    public function getAllTagGroups(): array
    {
        return $this->tagGroupRepo->findBy([], [
            'position' => "ASC"
        ]);
    }

    /**
     * @return array
     */
    public function getAllTags(): array
    {
        return $this->tagRepo->findAll();
    }

    /**
     * @param array $ids
     *
     * @return array
     */
    public function getTagsByIds(array $ids): array
    {
        $output = [];

        foreach ($ids as $id) {
            $output[] = $this->tagRepo->findOneBy([
                'id' => $id
            ]);
        }

        return $output;
    }
}