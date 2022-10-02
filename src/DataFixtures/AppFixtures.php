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

        $admin = new Users();
        $admin->setEmail('cyril@free.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPseudo('Cyril');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, '123456'));
        $manager->persist($admin);

        for ($a = 1; $a <= 20; $a++) {
            $article = new Articles();
            $article->setTitre($faker->sentence($nbWords = 6, $variableNbWords = true));
            $article->setContenu($faker->text());
            $article->setAuteur($admin);
            $article->setDatePublication(new \DatetimeImmutable());

            $manager->persist($article);
            
            // Utilisateurs
            for ($u = 1; $u <= 20; $u++) {
                $user = new Users();
                $user->setEmail($faker->email);
                $user->setRoles(['ROLE_USER']);
                $user->setPseudo($faker->firstName);
                $user->setPassword($this->passwordEncoder->hashPassword($user, 'secret'));
                
                // Commentaire par utilsateur
                for ($c = 1; $c <= 1; $c++) {
                    $comment = new Comments();
                    $comment->setContenu($faker->text());
                    $comment->setAuteur($user);
                    $comment->setArticle($article);
                    $comment->setDatePublication(new \DatetimeImmutable());
                    
                    $manager->persist($user);
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
