<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('animal')]
class AnimalController extends AbstractController
{
    #[Route('/alls/{page?1}/{nbre?15}', name: 'app_animal')]
    public function index(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Animal::class);
        $nbAnimal = $repository->count([]);
        $nbPage = ceil($nbAnimal / $nbre);
        $animals =  $repository->findBy([], [], $nbre, ($page -1) * $nbre);
        return $this->render('animal/index.html.twig', parameters: [
            'animals' => $animals,
            'isPaginated' => true,
            'nbPage' => $nbPage,
            'page' => $page,
            'nbre' => $nbre,
        ]);
    }

    #[Route('/edit/{id?0}', name: 'animal.edit')]
    public function addAnimal(Animal $animal = null,
                                ManagerRegistry $doctrine,
                                Request $request,
                                ): Response
    {
        $new = false;
        if (!$animal) {
            $new = true;
            $animal = new Animal();
        }

        $form = $this->createForm(AnimalType::class, $animal);
        //mon formulaire va aller traiter la requete
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($new) {
                $message = " a été ajouté avec succès";
            } else {
                $message = " a été mis à jour avec succès";
            }

            $manager = $doctrine->getManager();
            $manager->persist($animal);
            $manager->flush();

            $this->addFlash('success', $animal->getname() . $message);

            return $this->redirectToRoute('app_animal');
        } else {
            return $this->render('animal/add-animal.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/delete/{id}', name: 'animal.delete')]
    public function deleteAnimal(Animal $animal = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($animal) {
            $manager = $doctrine->getManager();
            $manager->remove($animal);

            $manager->flush();
            $this->addFlash('success', "l'animal a bien été supprimé");
        } else {
            $this->addFlash('error', "animal inexistant");
        }
        return $this->redirectToRoute('app_animal');
    }
}

