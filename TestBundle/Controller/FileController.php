<?php

namespace Hypo\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Hypo\TestBundle\Entity\File;
use Hypo\TestBundle\Form\FileType;

/**
 * File controller.
 *
 * @Route("/hypo_test_file")
 */
class FileController extends Controller
{
    /**
     * Lists all File entities.
     *
     * @Route("/", name="hypo_test_file")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('HypoTestBundle:File')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a File entity.
     *
     * @Route("/{id}/show", name="hypo_test_file_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('HypoTestBundle:File')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find File entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new File entity.
     *
     * @Route("/new", name="hypo_test_file_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new File();
        $form   = $this->createForm(new FileType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new File entity.
     *
     * @Route("/create", name="hypo_test_file_create")
     * @Method("post")
     * @Template("HypoTestBundle:File:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new File();
        $request = $this->getRequest();
        $form    = $this->createForm(new FileType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hypo_test_file_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing File entity.
     *
     * @Route("/{id}/edit", name="hypo_test_file_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('HypoTestBundle:File')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find File entity.');
        }

        $editForm = $this->createForm(new FileType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing File entity.
     *
     * @Route("/{id}/update", name="hypo_test_file_update")
     * @Method("post")
     * @Template("HypoTestBundle:File:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('HypoTestBundle:File')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find File entity.');
        }

        $editForm   = $this->createForm(new FileType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hypo_test_file_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a File entity.
     *
     * @Route("/{id}/delete", name="hypo_test_file_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('HypoTestBundle:File')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find File entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('hypo_test_file'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
