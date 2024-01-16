<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/profile/{id}', name:'app_user_profile')]
    public function editUserProfile(EntityManagerInterface $entityManager, Request $request, int $id): Response
    {
        $users = $entityManager->getRepository(User::class)->find($id);
        $usersForm = $this->createForm(UserType::class, $users);
        $usersForm->handleRequest($request);

        if ($usersForm->isSubmitted() && $usersForm->isValid()) {

            $users = $usersForm->getData();

            $entityManager->persist($users);
            $entityManager->flush();

            $this->addFlash('success', 'Profile edited successfully!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/editUser.html.twig', [
            'usersForm' => $usersForm
        ]);
    }
}
