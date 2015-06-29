<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ManagerController extends Controller
{
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_MANAGER')) {
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
            return $this->render('AppBundle:Manager:index.html.twig', [
                'users' => $users
            ]);
        }
        return $this->render('default/index.html.twig', [
            'errorMessages' => ['Access denied']
            ]
        );
    }
}