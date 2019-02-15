<?php

namespace App\Controller;

use App\Repository\CommentRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

use App\Form\ArticleType;
use App\Form\CommentType;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     */
    public function index(ArticleRepository $repo)
    {
        $articles = $repo->findBy([],['id' => 'DESC']);
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
                $article->setAuthor($this->getUser());
            }

            $manager->persist($article);
            $manager->flush();

            $this->addFlash('notice','Article créé avec succès');
            return $this->redirectToRoute('forum_show', ['id' => $article->getid()]);
        }
        return $this->render('forum/form.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }
    /**
     * [modif description]
     * @Route ("/forum/{id}/modif", name="forum_modif")
     */
    public function modif(Article $article = null, Request $request, ObjectManager $manager) {

        $this->denyAccessUnlessGranted("EDIT", $article);

        if(!$article) {
            $article = new Article();
        }

        // $form = $this->createFormBuilder($article)
        //              ->add('title')
        //              ->add('content')
        //              ->add('image')
        //              ->getForm();
        //

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()) {
                $article->setCreatedAt(new \Datetime);
                $article->setAuthor($this->getUser());
            }

            $manager->persist($article);
            $manager->flush();

            $this->addFlash('notice','Article créé avec succès');
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
    public function show(Request $request, ArticleRepository $repo, Article $article, ObjectManager $manager) {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $comment->setArticle($article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$comment->getId()) {
                        $comment->setCreatedAt(new \Datetime)
                                ->setAuthor($this->getUser())
                                ->setArticle($article);
            }

            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('notice','Commentaire créé avec succès');
            return $this->redirectToRoute('forum_show', ['id' => $article->getid()]);
        }

        return $this->render('forum/show.html.twig',[
            'formComment' => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * [delete description]
     * @Route ("/forum/delete/{id}", name="forum_delete")
     */
    public function delete(ArticleRepository $repo, Article $article, ObjectManager $manager) {

        $this->denyAccessUnlessGranted("DELETE", $article);

        $this->getUser()->removeArticle($article);
        $manager->remove($article);
        $manager->flush();

        $this->addFlash('notice','Article supprimé avec succès');

        return $this->redirectToRoute('forum');

    }
    /**
     * [deleteComment description]
     * @Route ("/forum/deleteComment/{id}", name="forum_deleteComment")
     */
    public function deleteComment(CommentRepository $repo, Comment $comment, ObjectManager $manager) {

        $this->denyAccessUnlessGranted("COMMENT_DELETE", $comment);

        $this->getUser()->removeComment($comment);
        $article = $comment->getArticle()->getId();
        $comment->getArticle()->removeComment($comment);
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash('notice','Commentaire supprimé avec succès');

        return $this->redirectToRoute('forum_show', ['id' => $article]);

    }
    /**
     * [AddRole description]
     * @Route ("/forum/addRole", name="forum_addrole")
     */
    public function addRole(ObjectManager $manager) {

        $this->getUser()->setRoles(['ROLE_ADMIN']);
        $manager->persist($this->getUser());
        $manager->flush();

        $this->addFlash('notice','Droit modifié avec succès');

        return $this->redirectToRoute('forum');

    }
}
