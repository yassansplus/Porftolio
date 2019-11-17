<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Blog;
use AppBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="adminHomepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Blog::class)->findAll();
        $numberComment = $em->getRepository(Comment::class)->findAll();
        $numberComment = count($numberComment);
        $sumVue =$em->getRepository(Blog::class)->createQueryBuilder('b')
           ->select('SUM(b.nbViewvers) as viewers')
            ->getQuery();
        $sumVue = $sumVue->getResult()[0]["viewers"];



        return $this->render('admin/default/index.html.twig', [
            'articles'=>$articles,
            'numberComment' => $numberComment,
            'numberVue' => $sumVue
        ]);
    }
}
