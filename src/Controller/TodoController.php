<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route("/todo")]
class TodoController extends AbstractController
{
    #[Route('/', name: 'app_todo')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        if (!$session->has('todos')) {
            $todos = [
                'achat'=>'acheter clé usb',
                'cours'=>'finaliser mon cours',
                'correction'=>'corriger mes exams'
            ];
            $session->set('todos', $todos);
            $this->addFlash('info', "la liste des todos vient d'être initialisée");
        }

        return $this->render('todo/index.html.twig');
    }
    #[Route('/add/{name?default}/{content?default}', name: 'todo.add')]
    public function addTodo(Request $request, $name, $content): RedirectResponse {
        $session = $request->getSession();

        if ($session->has('todos')) {
            $todos = $session->get('todos');

            if (isset($todos[$name])) {
                $this->addFlash('error', "le todo $name existe déjà dans la liste");
            } else {
                $todos[$name] = $content;
                $session->set('todos', $todos);
                $this->addFlash('success', "le todo $name a bien été ajouté à la liste");
            }
        } else {

            $this->addFlash('error', "la liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_todo');
    }
    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo(Request $request, $name): RedirectResponse {
        $session = $request->getSession();

        if ($session->has('todos')) {
            $todos = $session->get('todos');

            if (!isset($todos[$name])) {
                $this->addFlash('error', "le todo $name n'existe pas dans la liste");
            } else {
                unset($todos[$name]);
                $session->set('todos', $todos);
                $this->addFlash('success', "le todo $name a bien été supprimé de la liste");
            }
        } else {

            $this->addFlash('error', "la liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_todo');
    }
    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request, $name, $content): RedirectResponse {
        $session = $request->getSession();

        if ($session->has('todos')) {
            $todos = $session->get('todos');

            if (!isset($todos[$name])) {
                $this->addFlash('error', "le todo $name n'existe pas dans la liste");
            } else {
                $todos[$name] = $content;
                $this->addFlash('success', "le todo $name a été modifié avec succès");
                $session->set('todos', $todos);
            }
        } else {

            $this->addFlash('error', "la liste des todos n'est pas encore initialisée");
        }
        return $this->redirectToRoute('app_todo');
    }
    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo(Request $request): RedirectResponse {
        $session = $request->getSession();
        $session->remove('todos');
        return $this->redirectToRoute('app_todo');
    }
}
