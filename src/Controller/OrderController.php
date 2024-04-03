<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OrderItems;

class OrderController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private OrderRepository $orderRepository;
    private UserRepository $UserRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, UserRepository $UserRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->UserRepository = $UserRepository;
    }

    #[Route('/bestelling', name: 'app_order')]
    public function order(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        // Haal alle orders op uit de repository
        $orders = $this->orderRepository->findAll();

        // Haal alle gegevens van de ingelogde gebruiker op
        $user = $this->UserRepository->findAll();
        $user = $this->getUser();

        // Haal de ingelogde gebruiker zijn email op
        if ($user) {
            $loggedInUserEmail = $user->getEmail();
        }

        // Render het form
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('order.html.twig', [
                'form' => $form->createView(),
                'orders' => $orders,
            ]);
        }

        if ($request->isMethod('POST')) {
            if (!$this->getUser()) {
                $this->addFlash('error_login', 'Je moet ingelogd zijn om een bestelling te plaatsen.');
                return $this->redirectToRoute('app_order');
            }
        }

        $postcode = $form->get('postcode')->getData();
        $huisnummer = $form->get('huisnummer')->getData();


        $userEmail = $form->get('email')->getData();

        $orderItemsDataJSON = $request->request->get('orderItemsData');
        $orderItemsData = json_decode($orderItemsDataJSON, true);

        $itemDescriptions = [];
        $totalPrice = 0;
        $orderItems = [];

        foreach ($orderItemsData as $itemData) {
            $itemQuantity = $itemData['quantity'];
            $itemPrice = $itemData['price'];
            $itemName = $itemData['item'];

            $itemDescription = $itemQuantity . 'x ' . $itemName . ' €' . number_format($itemPrice, 2, '.', ',');

            $itemDescriptions[] = $itemDescription;
            $itemTotalPrice = $itemPrice;
            $totalPrice += $itemTotalPrice;

            $orderItems[] = [
                'item' => $itemName,
                'quantity' => $itemQuantity,
                'price' => $itemPrice,
                'total_price' => $itemTotalPrice,
            ];
        }

        $itemDescriptionString = implode(', ', $itemDescriptions);

        $orderItem = new OrderItems();
        $orderItem->setItem($itemDescriptionString);
        $orderItem->setPrice($totalPrice);

        $order->addOrderItem($orderItem);
        $this->entityManager->persist($orderItem);

        if (empty($orderItemsData)) {
            $this->addFlash('order_empty', 'U heeft nog geen producten toegevoegd.');
            return $this->redirectToRoute('app_order');
        }

        $user = $this->getUser();
        $order->setUsername($user);

        $orderedAt = new \DateTime();
        $order->setOrderedAt($orderedAt);

        $order->setDate($form->get('date')->getData());
        $order->setTime($form->get('time')->getData());

        $this->entityManager->persist($order);
        $this->entityManager->flush();


        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'live.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 587;
        $phpmailer->Username = 'api';
        $phpmailer->Password = '02f2f5bffcc043a266cd7481fdf9acc2';

        // test email smtp
        //        $phpmailer = new PHPMailer();
        //        $phpmailer->isSMTP();
        //        $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        //        $phpmailer->SMTPAuth = true;
        //        $phpmailer->Port = 2525;
        //        $phpmailer->Username = 'c1f81d14309b54';
        //        $phpmailer->Password = '602f783afea780';

        $phpmailer->setFrom('clearskysolar@niekkrammer.nl', 'clearskySolar');
        $phpmailer->addAddress($userEmail);
        $phpmailer->addAddress($loggedInUserEmail);
        $voornaam = $form->get('voornaam')->getData();
        $achternaam = $form->get('achternaam')->getData();

        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Bestelling clearskySolar';

        $orderDetails = '<p style="font-size: 15px; color: #0a0a0a;">Beste ' . $voornaam . ' ' . $achternaam . ', hier zijn de details van je bestelling:</p>';
        $orderDetails .= '<p style="color: #0a0a0a;">We komen langs op: ' . $order->getDate()->format('d-m-Y') . $order->getTime()->format(' H:i') . '</p>';
        $orderDetails .= '<a href="https://www.google.com/calendar/render?action=TEMPLATE&text=Bestelling%20bij%20ClearSkySolar&dates=' . $order->getDate()->format('Ymd') . 'T' . $order->getTime()->format('His') . 'Z/' . $order->getDate()->format('Ymd') . 'T' . $order->getTime()->format('His') . 'Z">Toevoegen aan Agenda</a>';
        $orderDetails .= '<h2 style="font-size: 18px; color: #0a0a0a;">Bestelde items:</h2>
                <ul>';
        foreach ($orderItems as $orderItem) {
            $itemQuantity = $orderItem['quantity'];
            $itemName = $orderItem['item'];

            $orderDetails .= '<li style="color: black;">' . $itemQuantity . 'x ' . $itemName . ' </li>';
        }
        $orderDetails .= '</ul>';
        $orderDetails .= '<p style="color: #0a0a0a;">Totale prijs: &euro;' . number_format($totalPrice, 2, '.', ',') . '</p>';

        $fontImport = '@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");';

        $phpmailer->Body = '<style>' . $fontImport . ' * { box-sizing: border-box; font-family: "Poppins", sans-serif; text-decoration: none !important; } </style>' .
            '<div style="background-color: #e9ecef; padding: 16px;">
         <h1 style="color: #0a0a0a; margin-top: 0; margin-bottom: 18px;">ClearSkySolar</h1>
            <h2 style="font-size: 22px; color: #0a0a0a;">Bevestiging van uw bestelling</h2>
            ' . $orderDetails . '
            <a href="http://localhost/clearskysolar/public/" style="background-color: #3b82f6; color: white; padding: 8px 24px; border-radius: 6px; text-decoration: none; border: none; 
            font-weight: bold;">Terug naar clearskySolar</a>
          </div>';

        try {
            $phpmailer->send();
            $this->addFlash('order_success', 'Je bestelling is succesvol geplaatst! Je ontvangt een e-mail ter bevestiging.');
            return $this->redirectToRoute('app_default', ['clearStorage' => 1]);

        } catch (Exception $e) {
            echo "Bericht kon niet worden verzonden. Mailerfout: {$phpmailer->ErrorInfo}";
            return $this->redirectToRoute('app_order');
        }

    }
}
