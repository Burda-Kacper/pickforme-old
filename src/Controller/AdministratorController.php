<?php

namespace App\Controller;

use App\Entity\Champion;
use App\Service\ChampionService;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministratorController extends AbstractController
{
    /**
     * @var ChampionService $championService
     */
    private ChampionService $championService;

    /**
     * @var TagService $tagService
     */
    private TagService $tagService;

    /**
     * @param ChampionService $championService
     * @param TagService $tagService
     */
    public function __construct(ChampionService $championService, TagService $tagService)
    {
        $this->championService = $championService;
        $this->tagService = $tagService;
    }

    /**
     * @return Response
     */
    public function champions(): Response
    {
        $champions = $this->championService->getAllChampions();

        return $this->render('administrator/champions.html.twig', [
            'champions' => $champions
        ]);
    }

    /**
     * @param int $championId
     *
     * @return JsonResponse
     */
    public function championsDetails(int $championId): JsonResponse
    {
        $championDetails = $this->championService->getChampionDetails($championId);
        $tagGroups = $this->tagService->getAllTagGroups();

        if ($championDetails) {
            return new JsonResponse([
                'success' => true,
                'view' => $this->renderView("administrator/_details.html.twig", [
                    'champion' => $championDetails,
                    'championTags' => $this->generateChampionTags($championDetails),
                    'tagGroups' => $tagGroups
                ])
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'message' => 'administrator.champions.not-found'
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function championsDetailsSave(Request $request): JsonResponse
    {
        $championId = $request->get('championId');
        $tagsIds = $request->get("tagsIds") ?? [];

        $allTags = $this->tagService->getAllTags();
        $tags = $this->tagService->getTagsByIds($tagsIds);

        return new JsonResponse(
            $this->championService->saveChampionDetails($championId, [
                'tags' => $tags,
                'allTags' => $allTags
            ])
        );
    }

    /**
     * @param Champion $champion
     *
     * @return array
     */
    private function generateChampionTags(Champion $champion): array
    {
        $output = [];

        foreach ($champion->getTags() as $tag) {
            $output = array_merge($output, [$tag->getId()]);
        }

        return $output;
    }
}
