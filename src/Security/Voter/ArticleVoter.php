<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Encapsule la logique des droits des utilisateurs concernant les objets Article
 */
class ArticleVoter extends Voter
{
    // Définition des attributs du Voter permettant d'attribuer des droits
    public const EDIT = 'CAN_EDIT'; // Droit de modifier un article
    public const DELETE = 'CAN_DELETE'; // Droit de supprimer un article

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Récupération de l'utilisateur authentifié
        $user = $token->getUser();
        // Si aucun utilisateur authentifié n'existe, le droit d'accès est refusé
        if (!$user instanceof UserInterface) {
            return false;
        }
         // Récupération du tableau des rôles de l'objet User authentifié
         $roles = $user->getRoles();
         // Récupération de l'objet User associé à l'objet Article
         $userArticle = $subject->getUser();

        // Evaluation des droits d'accès en fonction des attributs
        switch ($attribute) {
            case self::EDIT:
                // Logique permettant de déterminer le droit d'un utilisateur à modifier l'objet Article en fonction de son rôle

                // Si l'utilisateur a le rôle AUTHOR
                if(in_array("ROLE_AUTHOR", $roles)) {
                    // Retourne 'true' (accès autorisé) si l'utilisateur authentifié ayant le rôle AUTHOR est associé à l'objet Article - Sinon retourne 'false' (accès refusé)
                    return $userArticle === $user;

                // Si l'utilisateur à le rôle ADMIN
                } else if(in_array("ROLE_ADMIN", $roles)) {
                    // Retourne 'true' systématiquement (accès autorisé)
                    return true;
                }
                break;
            case self::DELETE:
                // Logique permettant de déterminer le droit d'un utilisateur à supprimer l'objet Article en fonction de son rôle
                
                // Si l'utilisateur a le rôle AUTHOR
                if(in_array("ROLE_AUTHOR", $roles)) {
                    // Retourne 'true' (accès autorisé) si l'utilisateur authentifié ayant le rôle AUTHOR est associé à l'objet Article - Sinon retourne 'false' (accès refusé)
                    return $userArticle === $user;

                // Si l'utilisateur à le rôle ADMIN
                } else if(in_array("ROLE_ADMIN", $roles)) {
                    // Retourne 'true' systématiquement (accès autorisé)
                    return true;
                }
                break;
        }
        return false;
    }
}
