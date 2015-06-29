<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\RoleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfileController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('security/profile.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}