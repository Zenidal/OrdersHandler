<?php

namespace AppBundle\Controller;

require_once('headers/headers.php');

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\RepairOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use AppBundle\Repository\RepairOrderRepository;

class HorderController extends Controller
{
    public function indexAction(Request $request)
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        /** @var RepairOrderRepository $repository */
        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:RepairOrder');

        $id = $request->get('id');

        if (is_null($id)) {
            /** @var RepairOrder[] $orders */
            $orders = $repository->findAll();
            $result = [];
            foreach ($orders as $order) {
                $result[] = [
                    'id' => $order->getId(),
                    'description' => $order->getDescription(),
                    'address' => $order->getAddress(),
                    'status' => $order->getTextStatus(),
                    'startDate' => is_null($order->getStartDate()) ? null : date("m.d.y", $order->getStartDate()->getTimestamp()),
                    'endDate' => is_null($order->getEndDate()) ? null : date("m.d.y", $order->getEndDate()->getTimestamp()),
                    'comment' => is_null($order->getComment()) ? null : $order->getComment(),
                    'user' => is_null($order->getUser()) ? null : [
                        'id' => $order->getUser()->getId(),
                        'username' => $order->getUser()->getUsername(),
                    ],
                    'company' => is_null($order->getCompany()) ? null : [
                        'id' => $order->getCompany()->getId(),
                        'name' => $order->getCompany()->getName(),
                    ],
                    'place' => is_null($order->getPlace()) ? null : [
                        'id' => $order->getPlace()->getId(),
                        'name' => $order->getPlace()->getName(),
                    ],
                    'engineer' => (is_null($order->getEngineer()) ? null : [
                        'id' => $order->getEngineer()->getId(),
                        'username' => $order->getEngineer()->getUsername(),
                    ]),
                ];
            }
        } else {
            /** @var RepairOrder $order */
            $order = $repository->find($id);
            $result = [
                'id' => $order->getId(),
                'description' => $order->getDescription(),
                'address' => $order->getAddress(),
                'status' => $order->getTextStatus(),
                'startDate' => is_null($order->getStartDate()) ? null : date("m.d.y", $order->getStartDate()->getTimestamp()),
                'endDate' => is_null($order->getEndDate()) ? null : date("m.d.y", $order->getEndDate()->getTimestamp()),
                'comment' => is_null($order->getComment()) ? null : $order->getComment(),
                'user' => is_null($order->getUser()) ? null : [
                    'id' => $order->getUser()->getId(),
                    'username' => $order->getUser()->getUsername(),
                ],
                'company' => is_null($order->getCompany()) ? null : [
                    'id' => $order->getCompany()->getId(),
                    'name' => $order->getCompany()->getName(),
                ],
                'place' => is_null($order->getPlace()) ? null : [
                    'id' => $order->getPlace()->getId(),
                    'name' => $order->getPlace()->getName(),
                ],
                'engineer' => (is_null($order->getEngineer()) ? null : [
                    'id' => $order->getEngineer()->getId(),
                    'username' => $order->getEngineer()->getUsername(),
                ]),
            ];
        }
        return new Response($serializer->serialize($result, 'json'));
    }
}