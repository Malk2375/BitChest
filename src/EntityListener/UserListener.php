<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
  // Pour hasher le mot de passe
  private UserPasswordHasherInterface $hasher;
  public function __construct(UserPasswordHasherInterface $hasher)
  {
    $this->hasher = $hasher;
  }
  // Pré-événement pour encoder le mot de passe avant la persistance de l'utilisateur.
  public function prePersist(User $user)
  {
    $this->encodePassword($user);
  }
  // Pré-événement pour encoder le mot de passe avant la mise à jour de l'utilisateur.
  public function preUpdate(User $user)
  {
    $this->encodePassword($user);
  }

  // Encode le mot de passe basé sur le mot de passe en clair.
  public function encodePassword(User $user)
  {
    // Vérifier si le mot de passe en clair est défini
    if ($user->getPlainPassword() === null) {
      return;
    }

    // Hasher le mot de passe et le définir pour l'utilisateur
    $user->setPassword(
      $this->hasher->hashPassword(
        $user,
        $user->getPlainPassword()
      )
    );
  }
}
