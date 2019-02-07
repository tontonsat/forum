<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;


use \App\Entity\User;
use \App\Form\RegistrationType;

class SecurityController extends AbstractController
{
    /**
     * [register description]
     * @Route("/register", name="security_register")
     * @param  Request       $request
     * @param  ObjectManager $manager
     * @return [type]                 [description]
     */
    public function register(Request $request, ObjectManager $manager) {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('security/register.html.twig',[
            'controller_name'   => 'SecurityController',
            'user'              => $user,
            'formUser'          => $form->createView()
        ]);
    }
}
