<?php

namespace App\Controller;

use App\Repository\VideoGameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
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
    public function helloWorld(): Response
    {
        $array = array(["hello" => "world"]);
        return $this->json($array);
    }

    #[Route('/api/gamelist', name: 'app_api_gamelist')]
    public function jsonGameList(VideoGameRepository $videoGameRepository): Response
    {
        $games = $videoGameRepository->findAll();
        $array = array();
        foreach ($games as $game) {
            array_push($array, ["id" => $game->getId(), "name" => $game->getName()]);
        }

        return $this->json($array);
    }

    #[Route('/api/convert', name: 'app_api_convert')]
    public function convertCsvToJson(): Response
    {
        $gamesInfo = array();
        if (($handle = fopen("D:/Programmes/Logiciels/laragon/www/ilovegamer/Ilovegamer/var/data/games.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                array_push($gamesInfo, $data);
            }
            fclose($handle);
        }

        return $this->json($gamesInfo);
    }

    #[Route('/api/gateway', name: 'app_api_gateway')]
    public function gateway(VideoGameRepository $videoGameRepository): Response
    {
        $videoGames = $videoGameRepository->findAll();
        $games = array();
        foreach ($videoGames as $game) {
            array_push($games, ["id" => $game->getId(),"name" => $game->getName()]);
        }
        $gamesInfo = array();
        if (($handle = fopen("C:/laragon/www/Ilovegamer/var/data/games.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                array_push($gamesInfo, $data);
            }
            fclose($handle);
        }

        $gameDatas = array();
        $indexsInDB = array();

        $gameInfoNameIndex = 0;
        $firstLine = true;
        foreach ($gamesInfo as $gameInfo) {
            
            if($firstLine)
            {
                for ($i=0; $i < count($gameInfo) ; $i++) {                
                    if($gameInfo[$i] == "Game Title")
                    {
                        $gameInfoNameIndex = $i;
                        break;
                    }
                }
                $firstLine = false;
            }
            else
            {
                $unique = true;
                foreach ($games as $game) {
                    if($game["name"] == $gameInfo[$gameInfoNameIndex])
                    {               
                        $unique = false;
                        unset($gameInfo[$gameInfoNameIndex]);
                        $callback = function() use ($gamesInfo, $gameInfo) : array
                        {
                            $values = array();
                            $key = 0;
                            foreach ($gamesInfo[0] as $gameInfoKey) {
                                if($gameInfoKey != "Game Title")
                                {
                                    array_push($values, [$gameInfoKey => $gameInfo[$key]]);
                                }
                                $key++;
                            }
                            return $values;
                        };
                        array_push($gameDatas, ["id" => $game["id"], "name"=> $game["name"],$callback()]);
                        
                        array_push($indexsInDB, $game["id"]);
                        break;
                    }
                }

                if($unique)
                {
                    array_push($gameDatas, $gameInfo);
                }
                
                
            }            
        }
        foreach ($games as $game) {
            if(!in_array($game["id"],$indexsInDB))
            {
                array_push($gameDatas, $game);
            }
        }

        return $this->json($gameDatas);
    }

    //#[Route('/api/routing/{format}', name: 'app_api_routing')]
    public function routing(string $format, VideoGameRepository $videoGameRepository): Response
    {
        $format = strtolower($format);
        $response = new Response();
        $response->headers->set('Content-Type', sprintf("text/%s",$format));        
        $response->setContent($format);
        
        return $this->render(sprintf("api/routing/%s_format.html.twig",$format), [
            'format' => $format,
        ]);
    }
}
