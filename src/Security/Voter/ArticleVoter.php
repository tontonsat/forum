<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;

class ArticleVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['EDIT', 'VIEW', 'DELETE', 'DELETEC'])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute($attribute, $article, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if( $article->getAuthor() === null) {
            return false;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                return $article->getAuthor()->getId() == $user->getId();
                break;
            case 'DELETE':
                // logic to determine if the user can DELETE
                // return true or false
                return $article->getAuthor()->getId() == $user->getId();
                break;
            case 'VIEW':
                // logic to determine if the user can view
                // return true or false
                break;
        }

        return false;
    }
}
