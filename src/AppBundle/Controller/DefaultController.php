<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Blog;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Job;
use AppBundle\Entity\Presentation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $presentation = $em->getRepository(Presentation::class)->findAll()[0];
        $jobs = $em->getRepository(Job::class)->findAll();


        // replace this example code with whatever you need
        return $this->render('default/index.html.twig',[
            'presentation' =>$presentation,
            'jobs' => $jobs
        ]);
    }
    /**
     * @Route("/blog", name="blogpage")
     */
    public function BlogAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/blog.html.twig');
    }


    /**
     * @Route("/blog/article/{article}", name="articlepage")
     */
    public function articleAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/article.html.twig');
    }




    /**
     * @Route("/isAdmin", name="isAdmin")
     */
    public function isAdminAction(Request $request)
    {
        $roles = $this->getUser()->getRoles();
        if ($roles[0] == "ROLE_ADMIN")
            return $this->redirectToRoute("adminHomepage");


    }




    /**
     * @Route("/image/{slug}.png", name="image")
     */
    public function imageBlogAction(Request $request)
    {
        $slug = $request->get('slug');
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository(Blog::class)->findOneBy(["slug"=> $slug ]);
        $image = $this->getParameter('brochures_directory') ."/". $blog->getImage();

            return new BinaryFileResponse($image);

    }
}
