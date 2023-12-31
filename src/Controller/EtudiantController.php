<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Validation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EtudiantType;
use App\Entity\Etudiant;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;


class EtudiantController extends AbstractController
{
    
    #[Route('/etudiants', name: 'etudiants')]
    public function etudiants(EntityManagerInterface $entityManager): Response
    {
        $etudiants = $entityManager->getRepository(Etudiant::class)->findAll();

        return $this->render('admin/etudiants.html.twig', ['etudiants' => $etudiants]);
    }

    #[Route('/nos_etudiants', name: 'nos_etudiants')]
    public function nos_etudiants(EntityManagerInterface $entityManager): Response
    {
        $validations = $entityManager->getRepository(Validation::class)->findAll();

        return $this->render('rh/nos_etudiants.html.twig', ['validations' => $validations]);
    }

    #[Route('/editer_etudiant/{id?0}', name: 'editer_etudiant')]
    public function editer_etudiant(Etudiant $etudiant = null, ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $new = false;
        if(!$etudiant){
            $new = true;
            $etudiant = new Etudiant();
        }

        $form = $this->createForm(EtudiantType::class, $etudiant);

        //dd($request->request);
        $form->handleRequest($request);

        if($form->isSubmitted()){

            $user = new User();

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setEmail($form->get('email')->getData());

            $etudiant->setPassword($user->getPassword());
            $etudiant->setEtatEtudiant("En attente");

            $manager = $doctrine->getManager();
            $manager->persist($etudiant);
            $manager->persist($user);
                
            $manager->flush();

            if($new){
                $message = "L'etudiant a été ajouter avec succès";
            }else{
                $message = "L'etudiant a été editer avec succès";
            }

            $this->addFlash("succes", $message);

            return $this->redirectToRoute("etudiants");
        }else{
            return $this->render('admin/add_etudiant.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/detail_etudiant/{id<\d+>}', name: 'detail_etudiant')]
    public function detail_etudiant(ManagerRegistry $doctrine, Etudiant $etudiant= null, $id): Response
    {
        if(!$etudiant){
            $this->addFlash('error', "Cet etudiant n'existe pas !");
            return $this->redirectToRoute("etudiants");
        }
        return $this->render('admin/etudiant_details.html.twig', ['etudiant' => $etudiant]);
    }

    #[Route('/delete_etudiant/{id?0}', name: 'delete_etudiant')]
    public function delete_etudiant(Etudiant $etudiant = null, ManagerRegistry $doctrine, Request $request, $id): Response
    {

        $repository = $doctrine->getRepository(Etudiant::class);
        $etudiant = $repository->find($id);

        $manager = $doctrine->getManager();
        $manager->remove($etudiant);

        $manager->flush();

        $message = "L'étudiant a été supprimer avec succès";


        $this->addFlash("succes", $message);

        return $this->redirectToRoute("etudiants");

    }

}
