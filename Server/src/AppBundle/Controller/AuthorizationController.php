<?php

namespace AppBundle\Controller;

require_once('headers/headers.php');

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AuthorizationController extends Controller
{

    public function indexAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $response = new Response();
        return $response->setContent(json_encode([
            'errorMessage' => 'Authorization was not successful',
            'lastUsername' => $lastUsername,
            'error' => $error
        ]), 200);
    }
}