<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime('@'. strtotime('now'));
        for ($i = 0; $i < 5; $i++) {
            $contact = new Contact();
            $contact->setFirstName('Anthony');
            $contact->setLastName('Haddad');
            $contact->setStreet('Ferdouse');
            $contact->setZipCode(961);
            $contact->setCity('Beirut');
            $contact->setCountry('Lebanon');
            $contact->setPhoneNumber('7154477');
            $contact->setBirthDate($date);
            $contact->setImgSrc('//unsplash.it/150/150');
            $contact->setEmail('haddad-anthony@live.com');
            $manager->persist($contact);
        };

        $manager->flush();
    }
}
