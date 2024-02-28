<?php

namespace App\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/registration', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('plainPassword')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();

            if ($password !== $confirmPassword) {
                $form->get('confirmPassword')->addError(new FormError('De wachtwoorden komen niet overeen.'));
                return $this->render('registration.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }   

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            $user->setCreatedAt(new \DateTime());
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('registration_success', 'Je registratie is succesvol voltooid! je kunt nu inloggen.');
            $this->sendEmail($user);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function sendEmail(User $user): void
    {
        $phpmailer = new PHPMailer();

        $phpmailer->isSMTP();
        $phpmailer->Host = 'live.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 587;
        $phpmailer->Username = 'api';
        $phpmailer->Password = '67a481dd00090e37922f82e2d6f458d4';

        $phpmailer->setFrom('qeuqen@niekkrammer.nl');
        $phpmailer->addAddress($user->getEmail(), $user->getUsername());
        $phpmailer->Subject = 'Registratiebevestiging qeuqen';
        $phpmailer->isHTML(true);
        $phpmailer->Body = $this->renderView('emails/registration.html.twig', [
            'username' => $user->getUsername(),
        ]);

        try {
            $phpmailer->send();
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
    }
}
