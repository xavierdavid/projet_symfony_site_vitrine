<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Encapsule la logique des droits des utilisateurs authentifiés concernant les objets User
 */
class UserVoter extends Voter
{
    // Définition des attributs du Voter permettant d'attribuer des droits
    public const DELETE = 'CAN_DELETE'; // Droit de supprimer un objet User

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE])
            && $subject instanceof \App\Entity\User;
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
        // Récupération du tableau des rôles de l'objet User à supprimer
        $userToDeleteRoles = $subject->getRoles();
        // Evaluation des droits d'accès en fonction des attributs
        switch ($attribute) {
            case self::DELETE:
                // Logique permettant de déterminer le droit d'un utilisateur authentifé à supprimer l'objet User en fonction de son rôle
                
                // Si l'utilisateur authentifié a le rôle ADMIN
                if(in_array("ROLE_ADMIN", $roles)) {
                    // Si l'objet User à supprimer à le rôle AUTHOR
                    if(in_array("ROLE_AUTHOR", $userToDeleteRoles)) {
                      // Retourne 'true' (accès autorisé)
                      return true; //
                    } else {
                      // Si l'objet User à supprimer a le role ADMIN, retourne 'false' (accès refusé)
                      return false;
                    }
                }
                break;
        }
        return false;
    }
}