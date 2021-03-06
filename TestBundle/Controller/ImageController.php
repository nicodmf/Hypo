<?php

namespace Hypo\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Hypo\TestBundle\Entity\Image;
use Hypo\TestBundle\Form\ImageType;

/**
 * Image controller.
 *
 * @Route("/hypo_test_image")
 */
class ImageController extends Controller
{
    /**
     * Lists all Image entities.
     *
     * @Route("/", name="hypo_test_image")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('HypoTestBundle:Image')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Image entity.
     *
     * @Route("/{id}/show", name="hypo_test_image_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('HypoTestBundle:Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Image entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new Image entity.
     *
     * @Route("/new", name="hypo_test_image_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Image();
        $form   = $this->createForm(new ImageType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Image entity.
     *
     * @Route("/create", name="hypo_test_image_create")
     * @Method("post")
     * @Template("HypoTestBundle:Image:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Image();
        $request = $this->getRequest();
        $form    = $this->createForm(new ImageType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hypo_test_image_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Image entity.
     *
     * @Route("/{id}/edit", name="hypo_test_image_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('HypoTestBundle:Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Image entity.');
        }

        $editForm = $this->createForm(new ImageType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Image entity.
     *
     * @Route("/{id}/update", name="hypo_test_image_update")
     * @Method("post")
     * @Template("HypoTestBundle:Image:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('HypoTestBundle:Image')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Image entity.');
        }

        $editForm   = $this->createForm(new ImageType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hypo_test_image_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Image entity.
     *
     * @Route("/{id}/delete", name="hypo_test_image_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('HypoTestBundle:Image')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Image entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('hypo_test_image'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
