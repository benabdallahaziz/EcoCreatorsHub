<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ArtistFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer User 1
        $user1 = new User();
        $user1->setEmail('marie@ecocreators.com');
        $user1->setUsername('marieeco');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'password'));
        $user1->setRoles(['ROLE_ARTIST']);
        $manager->persist($user1);

        // Artist 1
        $artist1 = new Artist();
        $artist1->setName('Marie EcoArt');
        $artist1->setBio('Artiste passionnée par les techniques écologiques et les pigments naturels. Je crée des œuvres avec des matériaux 100% naturels.');
        $artist1->setEcoTechnique('Pigments végétaux, terre naturelle, matériaux recyclés');
        $artist1->setProfilePicture('marie-eco.jpg');
        $artist1->setIsCertified(true);
        $artist1->setCreatedAt(new \DateTime());
        $artist1->setUser($user1);
        $manager->persist($artist1);

        // Créer User 2
        $user2 = new User();
        $user2->setEmail('pierre@ecocreators.com');
        $user2->setUsername('pierrenature');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'password'));
        $user2->setRoles(['ROLE_ARTIST']);
        $manager->persist($user2);

        // Artist 2
        $artist2 = new Artist();
        $artist2->setName('Pierre Naturel');
        $artist2->setBio('Spécialiste des sculptures en bois flotté et argile naturelle. Je valorise les matériaux trouvés dans la nature.');
        $artist2->setEcoTechnique('Bois flotté, argile naturelle, pierres');
        $artist2->setProfilePicture('pierre-nature.jpg');
        $artist2->setIsCertified(false);
        $artist2->setCreatedAt(new \DateTime());
        $artist2->setUser($user2);
        $manager->persist($artist2);

        $manager->flush();

        // Ajouter des références pour les utiliser dans d'autres fixtures
        $this->addReference('artist_marie', $artist1);
        $this->addReference('artist_pierre', $artist2);
    }
}