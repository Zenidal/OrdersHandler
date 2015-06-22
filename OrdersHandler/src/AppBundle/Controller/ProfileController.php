<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfileController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        return $this->render('security/profile.html.twig',
            array(
            ));
    }
}