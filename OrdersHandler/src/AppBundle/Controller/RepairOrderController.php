<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\RepairOrder;
use AppBundle\Form\Type\RepairOrderType;

/*?><script>alert('da')</script><?php*/

/**
 * RepairOrder controller.
 *
 * @Route("/repairorder")
 */
class RepairOrderController extends Controller
{

    /**
     * Lists all RepairOrder entities.
     *
     * @Route("/repair_orders", name="repair_orders")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:RepairOrder')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new RepairOrder entity.
     *
     * @Route("/create", name="repairorder_create")
     * @Method("POST")
     * @Template("AppBundle:RepairOrder:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new RepairOrder();
        $entity->setStatus(1);
        $user = $this->getDoctrine()->getManager()->find("AppBundle:User", 5);
        $entity->setUser($user);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('repairorder_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a RepairOrder entity.
     *
     * @param RepairOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RepairOrder $entity)
    {
        $form = $this->createForm(new RepairOrderType(), $entity, array(
            'action' => $this->generateUrl('repairorder_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new RepairOrder entity.
     *
     * @Route("/new", name="repairorder_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new RepairOrder();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a RepairOrder entity.
     *
     * @Route("/{id}", name="repairorder_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        return ['entity' => $entity];
    }

    /**
     * Displays a form to edit an existing RepairOrder entity.
     *
     * @Route("/{id}/edit", name="repairorder_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Creates a form to edit a RepairOrder entity.
     *
     * @param RepairOrder $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RepairOrder $entity)
    {
        $form = $this->createForm(new RepairOrderType(), $entity, array(
            'action' => $this->generateUrl('repairorder_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing RepairOrder entity.
     *
     * @Route("/{id}", name="repairorder_update")
     * @Method("PUT")
     * @Template("AppBundle:RepairOrder:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('repairorder_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        );
    }

    /**
     * Deletes a RepairOrder entity.
     *
     * @Route("/{id}/delete", name="repairorder_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {
        $deleteForm = $this->createDeleteForm($id);

        return array('delete_form' => $deleteForm->createView()
        );
    }

    /**
     * Creates a form to delete a RepairOrder entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('repairorder_remove', array(
                'id' => $id
            )))
            ->setMethod('POST')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    /**
     * @Route("/{id}/remove", name="repairorder_remove")
     * @Method("POST")
     * @Template("AppBundle:RepairOrder:delete.html.twig")
     */
    public function removeAction(Request $request, $id)
    {
        $deleteForm = $this->createDeleteForm($id);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:RepairOrder')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RepairOrder entity.');
        }

        $deleteForm->handleRequest($request);
        if ($deleteForm->isValid()) {
            $em->remove($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('repair_orders'));
        }
        return array('deleteForm' => $deleteForm->createView());
    }
}
