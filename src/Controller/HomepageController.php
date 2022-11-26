<?php

namespace App\Controller;

use App\Service\ChampionService;
use App\Service\ChooseService;
use App\Service\PFMLogService;
use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
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
     * @var ChooseService $chooseService
     */
    private ChooseService $chooseService;

    /**
     * @var PFMLogService $PFMLogService
     */
    private PFMLogService $PFMLogService;

    /**
     * @param ChampionService $championService
     * @param TagService $tagService
     * @param ChooseService $chooseService
     */
    public function __construct(ChampionService $championService, TagService $tagService, ChooseService $chooseService, PFMLogService $PFMLogService)
    {
        $this->championService = $championService;
        $this->tagService = $tagService;
        $this->chooseService = $chooseService;
        $this->PFMLogService = $PFMLogService;
    }

    /**
     * @return Response
     */
    public function homepage(): Response
    {
        return $this->render('homepage/homepage.html.twig');
    }

    /**
     * @return JsonResponse
     */
    public function pickRandom(): JsonResponse
    {
        $champions = $this->championService->getAllChampions();

        return new JsonResponse([
            'success' => true,
            'view' => $this->renderView('homepage/_random.html.twig', [
                'champions' => $champions
            ])
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function pickBattle(): JsonResponse
    {
        $champions = $this->championService->getAllChampions();
        shuffle($champions);
        shuffle($champions);
        shuffle($champions);

        return new JsonResponse([
            'success' => true,
            'view' => $this->renderView('homepage/_battle.html.twig', [
                'champions' => array_slice($champions, 0, 32)
            ])
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pickChoose(Request $request): JsonResponse
    {
        $tagGroups = $this->tagService->getTagGroupsForChoose();

        return new JsonResponse([
            'success' => true,
            'view' => $this->renderView('homepage/_choose.html.twig', [
                'tagGroups' => $tagGroups
            ])
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function chooseSubmit(Request $request): JsonResponse
    {
        $tags = $request->get("tags");
        $champions = $this->championService->getAllChampions();
        $champion = $this->chooseService->pickChooseChampion($champions, $tags);
        $this->PFMLogService->createLog("choose", $champion['id'], implode(",", $tags));

        return new JsonResponse([
            'success' => true,
            'champion' => $champion
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logBattle(Request $request): JsonResponse
    {
        $championId = $request->get('id');
        $this->PFMLogService->createLog("battle", (int)$championId);

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logRandom(Request $request): JsonResponse
    {
        $championId = $request->get('id');
        $this->PFMLogService->createLog("random", (int)$championId);

        return new JsonResponse(['success' => true]);
    }
}
