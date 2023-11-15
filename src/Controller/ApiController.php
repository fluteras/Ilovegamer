<?php

namespace App\Controller;

use App\Repository\VideoGameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
    #[Route('/api/helloworld', name: 'app_api_helloWorld')]
    public function HelloWorld(): Response
    {
        $array = array(["hello" => "world"]);
        return $this->json($array) ?? Response::HTTP_OK;
    }

    #[Route('/api/gamelist', name: 'app_api_gamelist')]
    public function GameList(VideoGameRepository $videoGameRepository): Response
    {

        $games = $videoGameRepository->findAll();
        $array = array();
        foreach ($games as $game) {
            array_push($array, ["id" => $game->getId()], ["name"=> $game->getName()]);
        }

        return $this->json(json_encode($array)) ?? Response::HTTP_NO_CONTENT;
    }
}
