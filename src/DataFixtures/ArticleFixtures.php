<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Faker;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On configure dans quelles langues nous voulons nos données
        $faker = Faker\Factory::create('fr_FR');

        for($i=1; $i < 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());
            $manager->persist($category);

            for($j=1; $j<= 10; $j++) {
                $article = new Article();
                $article->setTitle($faker->name)
                        ->setContent($faker->text($maxNbChars = 200))
                        ->setCategory($category)
                        ->setCreatedAt($faker->dateTime($max = 'now', $timezone = null))
                        ->setImage("http://placehold.it/350x150");
                $manager->persist($article);
            }
            $now = new \Datetime();
            $interval = $now->diff($article->getCreatedAt());
            $days = $interval->days;
            $minimum = "-". $days ." days";

            for ($k=0; $k < mt_rand(4, 10); $k++) {
                $comment = new Comment();
                $comment->setContent($faker->text($maxNbChars = 80))
                        ->setCreatedAt($faker->dateTime($minimum))
                        ->setArticle($article);
                $manager->persist($comment);

            }
        }
        $manager->flush();
    }
}
