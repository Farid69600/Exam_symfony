<?php

namespace App\Controller;

use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    /**
     * @Route ("/impression/new/{id}", name="impression_new")
     */
    public function new(Request $request, EntityManagerInterface $manager, Film  $film){

        $impression = new Impression();
        $formulaire = $this->createForm(ImpressionType::class, $impression);

        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted() && $formulaire->isValid()){

            $impression->setFilm($film);
            $impression->setUser($this->getUser());
            $manager->persist($impression);
            $manager->flush();
        }

        return $this->redirectToRoute('unfilm', ['id'=>$film->getId()]);
    }

    /**
     * @Route ("/impression/change/{id}", name="impression_change")
     * @return Response
     */
    public function change(Impression $impression, Request $request, EntityManagerInterface $manager, Film $film)
    {
        if(!$impression){return $this->redirectToRoute('film'); }

        if($impression->getUser() != $this->getUser()){
            return $this->redirectToRoute('film');}

        $formulaire = $this->createForm(ImpressionType::class, $film);
        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted()){
            $impression = $formulaire->getData();
            $manager->persist($impression);
            $manager->flush();

            return $this->redirectToRoute('unfilm', ['id'=>$impression->getFilm()->getId()]);
        }
        return $this->renderForm('impression/change.html.twig', ["formulaire" => $formulaire]);
    }
}
