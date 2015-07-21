<?php

namespace AppBundle\Controller;

require_once('headers/headers.php');

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthorizationController extends Controller
{
    public function indexAction(Request $request)
    {
        $security = $this->get('security.context');
        $user = $this->getUser();
        $roles = $user->getRoles();
        $token = new UsernamePasswordToken($user, null, $this->providerKey, $roles);
        $security->setToken($token);
    }
}