<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Presentation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Presentation controller.
 *
 * @Route("presentation")
 */
class PresentationController extends Controller
{
    /**
     * Lists all presentation entities.
     *
     * @Route("/", name="presentation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $presentations = $em->getRepository('AppBundle:Presentation')->findAll();

        return $this->render('presentation/index.html.twig', array(
            'presentations' => $presentations,
        ));
    }

    /**
     * Creates a new presentation entity.
     *
     * @Route("/new", name="presentation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $presentation = new Presentation();
        $request = $this->get('request_stack')->getMasterRequest();

        $form = $this->createForm('AppBundle\Form\PresentationType', $presentation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $lastPresentations = $em->getRepository(Presentation::class)->findAll();
            dump($lastPresentations);
            foreach ($lastPresentations as $aPresentation) {
                $em->remove($aPresentation);
            }
            $em->flush();
            $em = $this->getDoctrine()->getManager();
            $em->persist($presentation);
            $em->flush();


        }

        return $this->render('presentation/new.html.twig', array(
            'presentation' => $presentation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a presentation entity.
     *
     * @Route("/{id}", name="presentation_show")
     * @Method("GET")
     */
    public function showAction(Presentation $presentation)
    {
        $deleteForm = $this->createDeleteForm($presentation);

        return $this->render('presentation/show.html.twig', array(
            'presentation' => $presentation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing presentation entity.
     *
     * @Route("/{id}/edit", name="presentation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Presentation $presentation)
    {
        $deleteForm = $this->createDeleteForm($presentation);
        $editForm = $this->createForm('AppBundle\Form\PresentationType', $presentation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('presentation_edit', array('id' => $presentation->getId()));
        }

        return $this->render('presentation/edit.html.twig', array(
            'presentation' => $presentation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a presentation entity.
     *
     * @Route("/{id}", name="presentation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Presentation $presentation)
    {
        $form = $this->createDeleteForm($presentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($presentation);
            $em->flush();
        }

        return $this->redirectToRoute('presentation_index');
    }

    /**
     * Creates a form to delete a presentation entity.
     *
     * @param Presentation $presentation The presentation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Presentation $presentation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('presentation_delete', array('id' => $presentation->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
