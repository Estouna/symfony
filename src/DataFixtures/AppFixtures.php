<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Users;
use App\Entity\Articles;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;
use App\Entity\Comments;

class AppFixtures extends Fixture
{
    public function __construct(
        // Encode les mdp
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ) {
    }
    public function load(ObjectManager $manager): void
    {

        $faker = Faker\Factory::create('fr_FR');

        // 5 utilsateurs
        for ($u = 1; $u <= 5; $u++) {
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setRoles(['ROLE_USER']);
            $user->setPseudo($faker->firstName);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'secret'));

            // 10 articles par utilisateur
            for ($a = 1; $a <= 10; $a++) {
                $article = new Articles();
                $article->setTitre($faker->sentence($nbWords = 6, $variableNbWords = true));
                $article->setContenu($faker->text());
                $article->setAuteur($user);
                $article->setDatePublication(new \DatetimeImmutable());
                
                // 20 commentaires par utilsateur
                for ($c = 1; $c <= 2; $c++) {
                    $comment = new Comments();
                    $comment->setContenu($faker->text());
                    $comment->setAuteur($user);
                    $comment->setArticle($article);
                    $comment->setDatePublication(new \DatetimeImmutable());

                    $manager->persist($user);
                    $manager->persist($article);
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
