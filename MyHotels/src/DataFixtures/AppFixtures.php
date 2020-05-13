<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Tydes')
                  ->setLastName('Genie')
                  ->setEmail('ytegueu@yahoo.fr')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('https://banner2.kisspng.com/20180626/fhs/
                                kisspng-avatar-user-computer-icons-software-
                                developer-5b327cc98b5780.5684824215300354015708.jpg')
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);


        // Nous gérons les utilisateurs
        $users = [];
        $genres = ['male', 'female'];

        for($i = 1; $i <= 10; $i++){
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre))
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        // Nous gérons les annonces

        // Creation d'une annonce
        for($i = 1; $i <= 30; $i++) {
            $ad = new Ad();
        // Configuration d'une annonce
            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) -1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);
            // Création des différentes images
            for($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($image);
            }
                //Gestion des réservations
                for($j = 1; $j <= mt_rand(0, 10); $j++) {
                    $booking = new Booking();

                    $createdAt = $faker->dateTimeBetween('-6 months');
                    $startDate = $faker->dateTimeBetween('-3 months');
                // Gestion de la date de fin
                    $duration  = mt_rand(3, 10);
                    $endDate   = (clone $startDate)->modify("+$duration days");

                    $amount    = $ad->getPrice() * $duration;
                    $booker    = $users[mt_rand(0, count($users) -1)];
                    $comment   = $faker->paragraph();

                    $booking->setBooker($booker)
                            ->setAd($ad)
                            ->setStartDate($startDate)
                            ->setEndDate($endDate)
                            ->setCreatedAt($createdAt)
                            ->setAmount($amount)
                            ->setComment($comment);

                    $manager->persist($booking);

                    // Gestion des commentaires
                    if(mt_rand(0, 1)) {
                        $comment = new Comment();
                        $comment->setContent($faker->paragraph())
                                ->setRating(mt_rand(1, 5))
                                ->setAuthor($booker)
                                ->setAd($ad);

                        $manager->persist($comment);
                    }
                }

                $manager->persist($ad);
        }

        $manager->flush();
    }
}
