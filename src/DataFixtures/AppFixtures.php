<?php

namespace App\DataFixtures;

use App\Entity\Formation;
use App\Entity\Entreprise;
use App\Entity\Stage;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Création d'un générateur de données Faker
        $faker = \Faker\Factory::create('fr_FR');

        /******************************************
         ******** CREATION DES FORMATIONS *********     
         ******************************************/

        $DUTInfo = new Formation();
        $DUTInfo->setNomCourt("DUT Informatique");
        $DUTInfo->setNomLong("Diplôme Universitaire Technologique Informatique");

        $LPProg = new Formation();
        $LPProg->setNomCourt("LP Programmation Avancée");
        $LPProg->setNomLong("Licence Professionnelle Programmation Avancée");
        
        $DUTIC = new Formation();
        $DUTIC->setNomCourt("DU TIC");
        $DUTIC->setNomLong("Diplôme Universitaire Technologies de l'Information et de la Communication");

        $listForm = array($DUTInfo,$LPProg,$DUTIC);
        foreach ($listForm as $uneFormation)
        {
            $manager->persist($uneFormation);
        }

        /******************************************
         ******** CREATION DES ENTREPRISES ********     
         ******************************************/
        
        $nbEntreprise = $faker->numberBetween($min = 5, $max = 10);
        $listEntreprise = array();

        for ($i = 0; $i < $nbEntreprise ; $i++)
        {
            $entreprise = new Entreprise();
            $nomEntreprise = $faker->company;
            $nomEntrepriseSite = strtolower($nomEntreprise); // Chaîne en minuscule 
            $nomEntrepriseSite = str_replace(' ', '', $nomEntrepriseSite); // Chaîne sans espace
            $nomEntrepriseSite = str_replace('.', '', $nomEntrepriseSite); // Chaîne sans "."

            $entreprise->setNom($nomEntreprise);
            $entreprise->setActivite($faker->jobTitle);
            $entreprise->setAdresse($faker->address);
            $entreprise->setSite($faker->regexify('http\:\/\/'.'www\.'.$nomEntrepriseSite.'\.'.$faker->tld));

            // Ajout dans une liste
            $listEntreprise[] = $entreprise;
        }
        foreach ($listEntreprise as $uneEntreprise)
        {
            $manager->persist($uneEntreprise);
        }

        /******************************************
         ******** CREATION DES STAGES *************     
         ******************************************/

        $nbStage = $faker->numberBetween($min = 5, $max = 10);
        $listStage = array();

        for ($i = 0; $i < $nbStage ; $i++)
        {
            $stage = new Stage();

            $stage->setTitre($faker->jobTitle);
            $stage->setDescription($faker->realText($maxNbChars = 255, $indexSize = 3));
            $stage->setEmail($faker->email);
            
            // Attribution d'une entreprise au stage
            $indiceEntreprise = $faker->numberBetween($min = 0, $max = sizeof($listEntreprise)-1);
            $stage->setMonEntreprise($listEntreprise[$indiceEntreprise]);
            
            // Attribution d'une ou plusieurs formations au stage
            $nbForm = $faker->numberBetween($min = 0, $max = sizeof($listForm)-1);
            for ($j = 0 ; $j <= $nbForm; $j++){
                $numFormation = $faker->numberBetween($min = 0, $max = sizeof($listForm)-1);
                $stage->addMesFormation($listForm[$numFormation]);
            }
            
            // Ajout dans une liste
            $listStage[] = $stage;
        }
        foreach ($listStage as $unStage)
        {
            $manager->persist($unStage);
        }        

        /******************************************
         ******** CREATION DES USERS **************     
         ******************************************/
        $tanguy = new User();
        $tanguy->setPrenom("Tanguy");
        $tanguy->setNom("Mossion");
        $tanguy->setEmail("tanguy@free.fr");
        $tanguy->setRoles(["ROLE_USER", "ROLE_ADMIN"]);
        $tanguy->setPassword('$2y$15$CvnkOss0Kpvq/2Pr1gips.gY0Ng/5vzFfH6Ft00I9aL6XBl0.9xJ2');

        $manager->persist($tanguy);

        $myUser = new User();
        $myUser->setPrenom("MyUserPrenom");
        $myUser->setNom("MyUserNom");
        $myUser->setEmail("myuser@free.fr");
        $myUser->setRoles(["ROLE_USER"]);
        $myUser->setPassword('$2y$15$FlNQhixQxp9fzfrvWu6hQ.d9ZQ05EWkr5zba1LBcxK6saKFzWTk4K');

        $manager->persist($myUser);

        // Envoyer les données en BD
        $manager->flush();
    }
}
