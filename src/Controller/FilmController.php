<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    #[Route('/film', name: 'films')]
    public function index(FilmRepository $repo): Response
    {
        return $this->render('film/index.html.twig', [
            'films' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/unfilm/{id}", name="unfilm")
     * @return Response
     */
    public function show(Film $film, Request $request, EntityManagerInterface $manager): Response
    {
        $impression = new Impression();
        $formulaireImpression = $this->createForm(ImpressionType::class, $impression);

        $formulaireImpression->handleRequest($request);

        if($formulaireImpression->isSubmitted()){
            $impression->setFilm($film);
            $manager->persist($impression);
            $manager->flush();
        }
        return $this->renderForm('film/show.html.twig', [
            'film' => $film,
            'formulaireImpression'=>$formulaireImpression
        ]);
    }


    /**
     * @Route("/film/new", name="film_new")
     */
    public function new(Request $laRequete, EntityManagerInterface $manager){

        $film = new Film();
        $formulaire = $this->createForm(FilmType::class, $film);

        $formulaire->handleRequest($laRequete);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {

            $film->setCreatedAt(new \DateTime());
            $film->setUser($this->getUser());
            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('film');
        }

        return $this->renderForm('film/new.html.twig', ["leFormulaire" => $formulaire]);
    }

    /**
     * @Route("/supprimerfilm/{id}", name="supprimerfilm")
     * @return Response
     */
    public function suppr(Film $film, EntityManagerInterface $manager)
    {
        if ($film && $film->getUser() == $this->getUser()) {
            $manager->remove($film);
            $manager->flush();
        }
        return $this->redirectToRoute('film');
    }


    /**
     * @Route("/film/change/{id}", name="change", priority="2")
     */
    public function change(Film $film, Request $request, EntityManagerInterface $manager){

        $formulaire = $this->createForm(FilmType::class, $film);
        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted() && $formulaire->isValid()){
            $film = $formulaire->getData();
            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('unfilm', ['id'=>$film->getId()]);
        }
        return $this->renderForm('film/change.html.twig', ["formulaire" => $formulaire]);
    }


}
    
    
    

