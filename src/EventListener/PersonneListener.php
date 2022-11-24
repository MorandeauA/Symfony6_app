<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonneEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener
{
    public function __construct(private LoggerInterface $logger) {}

    public function onPersonneAdd(AddPersonneEvent $event) {
        $this->logger->debug("cc je suis en train d'écouter l'évenement personne.add et une personne vient d'être ajoutée : ". $event->getPersonne()->getName());
    }

    public function onListAllPersonnes(ListAllPersonneEvent $event) {
        $this->logger->debug("Le nombre de personnes dans la base est ". $event->getNbPersonne());
    }

    public function logKernelRequest(KernelEvent $event) {
        dd($event->getRequest());
    }
}
