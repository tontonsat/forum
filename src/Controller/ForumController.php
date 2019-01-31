<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Article;
use App\Repository\ArticleRepository;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     */
    public function index(ArticleRepository $repo)
    {
        $articles = $repo->findAll();
        return $this->render('forum/index.html.twig', [
            'controller_name'   => 'ForumController',
            'articles'          => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('forum/home.html.twig', [
            'age' => 14
        ]);
    }

    /**
     * [create description]
     * @Route ("/forum/new", name="forum_create")
     */
    public function create(Request $request, ObjectManager $manager) {
        $article = new Article();
        $form = $this->createFormBuilder($article)
                     ->add('title')
                     ->add('content')
                     ->add('image')
                     ->getForm();
        return $this->render('forum/create.html.twig', [
            'formArticle' => $form->createView()
        ]);
    }

    /**
     * [show description]
     * @Route ("/forum/show/{id}", name="forum_show")
     */
    public function show(ArticleRepository $repo, Article $article) {
        return $this->render('forum/show.html.twig',[
            'article' => $article
        ]);
    }
}
