<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ManagerController extends Controller
{
    //!!!!!!!!!!!Do redirect if not a manager goes to href /manager
    public function indexAction(){
        $role = $this->getUser()->getRole();
        $this->denyAccessUnlessGranted($this->denyAccessUnlessGranted('view', $role, 'Access denied!'));
        return $this->render('AppBundle:Manager:index.html.twig');
    }
}