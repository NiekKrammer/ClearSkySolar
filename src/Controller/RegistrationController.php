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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;
    private TokenStorageInterface $tokenStorage;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        TokenStorageInterface       $tokenStorage,
        EventDispatcherInterface    $eventDispatcher
    )
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Route('/registration', name: 'app_register')]
    public function register(Request $request): Response
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
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );

            $user->setCreatedAt(new \DateTime());
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('registration_success', 'Je registratie is succesvol voltooid!');
            $this->sendEmail($user);

            // Handmatig inloggen van de gebruiker na registratie
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);

            // Dispatch de login event
            $event = new InteractiveLoginEvent($request, $token);
            $this->eventDispatcher->dispatch($event);

            return $this->redirectToRoute('app_default');
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

        $phpmailer->setFrom('clearskysolar@niekkrammer.nl');
        $phpmailer->addAddress($user->getEmail(), $user->getUsername());
        $phpmailer->Subject = 'Registratiebevestiging ClearSkySolar';
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
