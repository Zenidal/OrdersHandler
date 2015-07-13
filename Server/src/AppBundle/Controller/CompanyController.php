<?php

namespace AppBundle\Controller;
header("Access-Control-Allow-Origin: *");

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class CompanyController extends Controller
{
    public function indexAction()
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Company');
        $companies = $repository->findAll();
        $result = [];
        foreach($companies as $company){
            $result[] = ['id' => $company->getId(), 'name' => $company->getName()];
        }
        return new Response($serializer->serialize($result, 'json'));
    }
}