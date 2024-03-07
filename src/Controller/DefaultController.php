<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ContactFormType;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DefaultController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        $productsRepository = $this->entityManager->getRepository(Products::class);
        $products = $productsRepository->findBy(['available' => true]);

        $isLoggedIn = $this->security->isGranted('ROLE_USER');

        return $this->render('homepage.html.twig', [
            'controller_name' => 'DefaultController',
            'products' => $products,
            'isLoggedIn' => $isLoggedIn,
        ]);
    }

    #[Route('/statistieken', name: 'app_statistieken')]
    public function viewStatistics(): Response
    {
        return $this->render('statistieken.html.twig');
    }

    #[Route('/product/{name}', name: 'product_view')]
    public function viewProduct($name): Response
    {
        $product = $this->entityManager->getRepository(Products::class)->findOneBy(['name' => $name]);

        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }

        return $this->render('product.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $phpmailer = new PHPMailer(true);
            try {
                $phpmailer->isSMTP();
                $phpmailer->Host = 'live.smtp.mailtrap.io';
                $phpmailer->SMTPAuth = true;
                $phpmailer->Port = 587;
                $phpmailer->Username = 'api';
                $phpmailer->Password = '67a481dd00090e37922f82e2d6f458d4';

                $phpmailer->setFrom('clearskysoloar@niekkrammer.nl');
                $phpmailer->addAddress('niekkrammer@gmail.com', 'ClearSkySolar');
                $phpmailer->addAddress('6000785@mborijnland.nl', 'ClearSkySolar');
                $phpmailer->isHTML(true);
                $phpmailer->Subject = 'Contact Formulier';
                $phpmailer->Body = "Naam: {$formData['firstName']} {$formData['lastName']}<br>E-mail: {$formData['email']}<br>Bericht: {$formData['message']}";

                $this->addFlash('contact_success', 'Je enail is succesvol verstuurd! We nemen zo snel mogelijk contact met u op.');
                $phpmailer->send();

                return $this->redirectToRoute('app_contact');
            } catch (Exception $e) {
                return new Response('Het bericht kon niet worden verzonden. Mailer-fout: ' . $phpmailer->ErrorInfo);
            }
        }

        return $this->render('contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
