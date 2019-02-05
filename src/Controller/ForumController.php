<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use App\Form\ArticleType;
use App\Form\CommentType;
use App\Entity\Article;
use App\Entity\Comment;
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
     * [addComment description]
     * @Route ("/forum/new", name="forum_create")
     * @Route ("/forum/{id}/edit", name="forum_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager) {

        if(!$article) {
            $article = new Article();
        }

        // $form = $this->createFormBuilder($article)
        //              ->add('title')
        //              ->add('content')
        //              ->add('image')
        //              ->getForm();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()) {
                $article->setCreatedAt(new \Datetime);
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('forum_show', ['id' => $article->getid()]);
        }
        return $this->render('forum/form.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * [comment description]
     * @Route ("/forum/{id}/comment", name="forum_comment")
     */
    public function comment(Comment $comment, Article $article, Request $request, ObjectManager $manager) {

        $form->handleRequest($request);
        $data = $form->getData();
        dump($data);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$comment->getId()) {
                $comment->setCreatedAt(new \Datetime);
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('forum_show', ['id' => $article->getid()]);
        }
        return $this->render('forum/form.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * [show description]
     * @Route ("/forum/show/{id}", name="forum_show")
     */
    public function show(ArticleRepository $repo, Article $article) {

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('forum/show.html.twig',[
            'formComment' => $form->createView(),
            'article' => $article
        ]);
    }
}
