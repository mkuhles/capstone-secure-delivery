<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class XssCheckController extends AbstractController
{
    #[Route('/xss/unsafe', name: 'app_xss_unsafe')]
    public function unsafe(Request $request): Response
    {
        $msg = (string) $request->query->get('msg', '');
        
        return $this->render('xss_check/unsafe.html.twig', [
            'controller_name' => 'XssCheckController',
            'msg' => $msg,
        ]);
    }

    #[Route('/xss/safe', name: 'app_xss_safe')]
    public function safe(Request $request): Response
    {
        $msg = (string) $request->query->get('msg', '');

        return $this->render('xss_check/safe.html.twig', [
            'controller_name' => 'XssCheckController',
            'msg' => $msg,
        ]);
    }
}
