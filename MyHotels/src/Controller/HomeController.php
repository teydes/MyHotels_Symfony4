<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/bonjour/{prenom}/{age}", name="hello")
     * @Route("/salut", name="hello_base")
     *@Route("/bonjour/{prenom}", name="hello prenom")
     *
     * Montre la page qui dit bonjour
     *
     * @return Response
     */
    public function hello($prenom = "anonyme", $age = 0){
        return $this->render(
            'hello.html.twig',
            [
                'prenom' => $prenom,
                'age' => $age
            ]
        );
    }
    /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo)
    {
        $prenoms = ["Jean" => 12,"Pierre" => 30,"Jacques" => 42];

        return $this->render(
            'home.html.twig',
            [
                'ads' => $adRepo->findBestAds(3),
                'users' => $userRepo->findBestUsers(2)
            ]
        );
    }
}