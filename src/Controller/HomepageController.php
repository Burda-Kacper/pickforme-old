<?php

namespace App\Controller;

use App\Service\ChampionService;
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
     * @param ChampionService $championService
     */
    public function __construct(ChampionService $championService)
    {
        $this->championService = $championService;
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function pickChoose(Request $request): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'view' => $this->renderView('homepage/_choose.html.twig')
        ]);
    }
}
