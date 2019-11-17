<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Blog;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Presentation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Blog controller.
 *
 * @Route("portfolio")
 */
class BlogController extends Controller
{


    /**
     * Lists all blog entities.
     *
     * @Route("/", name="blog_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

//        $blogs = $em->getRepository('AppBundle:Blog')->findAll();
        $presentation = $em->getRepository(Presentation::class)->findAll()[0];

        $dql   = "SELECT a FROM AppBundle:Blog a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );

        return $this->render('blog/index.html.twig', array(
            'blogs' => $pagination,
            'presentation' => $presentation
        ));
    }

    /**
     * Creates a new blog entity.
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/new",  name="blog_new",  )
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

        $blog = new Blog();
        $request = $this->get('request_stack')->getMasterRequest();
        $form = $this->createForm('AppBundle\Form\BlogType', $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //upload de l'image ici !!!!


            $brochureFile = $form['image']->getData();


            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $blog->setImage($newFilename);

            }


            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();

            //return $this->redirectToRoute('adminHomepage');
        }

        return $this->render('blog/new.html.twig', array(
            'blog' => $blog,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a blog entity.
     *
     * @Route("/{slug}",  options={"expose"=true}, name="blog_show")
     * @Method("GET")
     */
    public function showAction(Request $request)
    {
        $slug = $request->get('slug');
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository(Blog::class)->findOneBy(["slug" => $slug]);
        $blogs = $em->getRepository(Blog::class)->findAll();
        $presentation = $em->getRepository(Presentation::class)->findAll()[0];
        $blog->setNbViewvers($blog->getNbViewvers() + 1);
        $em->flush();
        $deleteForm = $this->createDeleteForm($blog);

        return $this->render('blog/show.html.twig', array(
            'blogs' => $blogs,
            'blog' => $blog,
            'delete_form' => $deleteForm->createView(),
            'presentation' => $presentation
        ));
    }

    /**
     * Displays a form to edit an existing blog entity.
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/edit",  options={"expose"=true}, name="blog_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Blog $blog)
    {
        $em = $this->getDoctrine()->getManager();
        $blog->setTitle($request->get('titre'));
        $blog->setBlogArticle($request->get('article'));
        $blog->setSlug($request->get('slug'));
        $blog->setPublished($request->get('published') == "on" ? true : false);
        $em->persist($blog);
        $em->flush();
        return new JsonResponse("et voila! votre Post a été modifié ! 🤗");
    }

    /**
     * Deletes a blog entity.
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}", options={"expose"=true}, name="blog_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Blog $blog)
    {

        $form = $this->createDeleteForm($blog);

        $request = $this->get('request_stack')->getMasterRequest();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($blog);
            $em->flush();
        }

        return new JsonResponse("ok");
    }


    /**
     * Displays a form to edit an existing blog entity.
     *
     * @Route("/comment/{id}", options={"expose"=true}, name="post_comment")
     * @Method({"GET", "POST"})
     */
    public function postCommentAction(Request $request, Blog $blog)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $comment->setEmail($request->get('email'));
        $comment->setCommentary($request->get('message'));
        $comment->setName($request->get('nom'));
        $comment->setBlog($blog);
        $em->persist($comment);
        $em->flush();

        return new JsonResponse("Et voilà votre message est envoyé !");


    }

    /**
     * get a blog entity.
     *
     * @Route("/getArticle/{id}", options={"expose"=true}, name="getArticle")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method({"GET", "POST"})
     */
    public function getArticleAction(Request $request, Blog $blog)
    {

        $encoders = ["xml" => new XmlEncoder(), 'json' => new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
        $data = $serializer->serialize($blog, 'json');

        return new JsonResponse($data);

    }

    /**
     * get a blog entity.
     *
     * @Route("/getArticles/", options={"expose"=true}, name="getArticles")
     * @Method({"GET", "POST"})
     */
    public function getAllArticlesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Blog::class)->findAll();
        $encoders = ["xml" => new XmlEncoder(), 'json' => new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
// Add Circular reference handler
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);
        $data = $serializer->serialize($articles, 'json');

        return new JsonResponse($data);

    }

    /**
     * Creates a form to delete a blog entity.
     *
     * @param Blog $blog The blog entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Blog $blog)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('blog_delete', array('id' => $blog->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }


}

