<?php

namespace App\Controller;

use App\Entity\Note;
use App\Security\Voter\NoteVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NoteController extends AbstractController
{    
    #[Route('/note', name: 'note_all')]
    public function index(): Response
    {
        $user = $this->getUser();
        if($user instanceof \App\Entity\User === false) {
            throw $this->createAccessDeniedException('You must be logged in to view notes.');
        }
        
        return $this->render('note/index.html.twig', [
            'notes' => $user->getNotes(),
            'controller_name' => 'NoteController',
        ]);
    }

    #[Route('/note/{id}', name: 'note_show')]
    public function note_show(Note $note): Response
    {
        $this->denyAccessUnlessGranted(NoteVoter::VIEW, $note);

        return $this->render('note/note_show.html.twig', [
            'note' => $note,
            'controller_name' => 'NoteController',
        ]);
    }
}
