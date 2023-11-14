<?php

namespace App\Controller;

use App\Entity\VideoGame;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\VideoGameRepository;

class PlatformController extends AbstractController
{
    #[Route('/platform', name: 'app_platform')]
    public function index(): Response
    {
        return $this->render('platform/index.html.twig', [
            'controller_name' => 'PlatformController',
        ]);
    }

    #[Route('/platform/{name}', name: 'app_platform_name')]
    public function SelectPlateform(string $name, UserRepository $userRepository,VideoGameRepository $videoGameRepository)
    {
        $name = str_replace('¨', '/', $name);
        $user = $this->getUser();
        $videoGameNames = $userRepository->findVideoGamesByPlatformByUser($user, $name);
        $videoGames = array();
        foreach ($videoGameNames as $gameName) {
            array_push($videoGames, $videoGameRepository->findOneBy(['name' => $gameName['name']]));
        }
        
        //dd($videoGames);
        return $this->render('platform/index.html.twig', [
            'videoGames' => $videoGames,
        ]);
    }
}
