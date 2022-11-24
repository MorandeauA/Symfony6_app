<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use App\Form\PersonneType;
use App\Service\Helpers;
use App\Service\MailerService;
use App\Service\PdfService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\TextUI\Help;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[
    Route('personne'),
    IsGranted("ROLE_USER")
]

class PersonneController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private Helpers $helper,
        private EventDispatcherInterface $dispatch
    ) {}

    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }

    #[Route('/pdf/{id}', name: 'personne.pdf')]
    public function generatePdfPersonne(Personne $personne = null, PdfService $pdf) {
        $html = $this->render('personne/detail.html.twig', ['personne' => $personne]);
        $pdf->showPdfFile($html);
    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', ['personnes' => $personnes]);
    }

    #[Route('/alls/{page?1}/{nbre?15}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $nbPersonne = $repository->count([]);
        $nbPage = ceil($nbPersonne / $nbre);
        $personnes = $repository->findBy([], [], $nbre, ($page -1) * $nbre);

        $listAllPersonneEvent = new ListAllPersonneEvent(count($personnes));
        $this->dispatch->dispatch($listAllPersonneEvent, ListAllPersonneEvent::LIST_ALL_PERSONNE_EVENT);

        return $this->render('personne/index.html.twig', parameters: [
            'personnes' => $personnes,
            'isPaginated' => true,
            'nbPage' => $nbPage,
            'page' => $page,
            'nbre' => $nbre,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response {
        if (!$personne) {
            $this->addFlash('error', "la personne n'existe pas");
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/detail.html.twig', ['personne' => $personne]);
    }

    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(Personne $personne = null,
                                ManagerRegistry $doctrine,
                                Request $request,
                                UploaderService $uploaderService,
                                MailerService $mailer
                                ): Response
    {
        $new = false;
        if (!$personne) {
            $new = true;
            $personne = new Personne();
        }

        //$personne est l'image de notre formulaire
        $form = $this->createForm(PersonneType::class,$personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        //mon formulaire va aller triater la requete
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form->get('photo')->getData();
            if ($photo) {

                $directory = $this->getParameter('personne_directory');
                $personne->setImage($uploaderService->uploadFile($photo, $directory));
            }
            if ($new) {
                $message = " a été ajouté avec succès";
                $personne->setCreatedBy($this->getUser());
            } else {
                $message = " a été mis à jour avec succès";
            }

            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();

            if ($new) {
                //création évenement
                $addPersonneEvent = new AddPersonneEvent($personne);
                //on va le dispatcher
                $this->dispatch->dispatch($addPersonneEvent, AddPersonneEvent::ADD_PERSONNE_EVENT);
            }
            $this->addFlash('success', $personne->getFirstname() . $message);

            return $this->redirectToRoute('personne.list.alls');
        } else {
            return $this->render('personne/add-personne.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[
        Route('/delete/{id}', name: 'personne.delete'),
        IsGranted('ROLE_ADMIN')
    ]
    public function deletePersonne(Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
        if ($personne) {
            $manager = $doctrine->getManager();
            $manager->remove($personne);

            $manager->flush();
            $this->addFlash('success', "la personne a été supprimé avec succès");
        } else {
            $this->addFlash('error', "personne inexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }

    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne(Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age): RedirectResponse
    {
        if ($personne) {
            $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);

            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();

            $this->addFlash('success', "la personne a été modifié avec succès");
        } else {
            $this->addFlash('error', "personne inexistante");
        }
        return $this->redirectToRoute('personne.list.alls');
    }
}
