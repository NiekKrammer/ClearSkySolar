<?php
namespace App\Controller;

use App\Repository\OrderRepository;
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

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
    }


    #[Route('/bestelling', name: 'app_order')]
    public function order(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        // Haal alle orders op uit de repository
        $orders = $this->orderRepository->findAll();

        // Render het form
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('order.html.twig', [
                'form' => $form->createView(),
                'orders' => $orders,
            ]);
        }

        if ($request->isMethod('POST')) {
            if (!$this->getUser()) {
                $this->addFlash('error', 'Je moet ingelogd zijn om een bestelling te plaatsen.');
                return $this->redirectToRoute('app_order');
            }
        }

        $userEmail = $form->get('email')->getData();
        $userAddress = $form->get('address')->getData();

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
        $phpmailer->Username = 'api';
        $phpmailer->Password = '67a481dd00090e37922f82e2d6f458d4';
        $phpmailer->Port = 587;

        $phpmailer->setFrom('clearskysolar@niekkrammer.nl', 'ClearSkySolar');
        $phpmailer->addAddress($userEmail);

        $name = $form->get('name')->getData();

        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Bestelling ClearSkySolar';

        $orderDetails = '<p style="font-size: 15px; color: #0a0a0a;">Beste ' . $name . ', hier zijn de details van je bestelling:</p>';
        $orderDetails .= '<p style="color: #0a0a0a;">Je bestelling wordt geleverd op ' . $order->getDate()->format('d-m-Y') . $order->getTime()->format(' H:i') . '</p>';

        $orderDetails .= '<h2 style="font-size: 18px; color: #0a0a0a;">Bestelde items:</h2>
                <ul>';
        foreach ($orderItems as $orderItem) {
            $itemQuantity = $orderItem['quantity'];
            $itemName = $orderItem['item'];

            $orderDetails .= '<li style="color: black;">' . $itemQuantity . 'x ' . $itemName . ' </li>';
        }
        $orderDetails .= '</ul>';

        $orderDetails .= '<p style="color: #0a0a0a;">Totale prijs: &euro;' . number_format($totalPrice, 2, '.', ',') . '</p>';

        $phpmailer->Body = '<div style="background-color: rgba(48,75,231,0.54); padding: 20px;">
                <h1 style="margin: 0; padding: 0; color: #0a0a0a;">ClearSkySolar</h1>
                    <h1 style="font-size: 22px; color: #0a0a0a;">Bevestiging van uw bestelling</h1>
                    ' . $orderDetails . '
                    <a href="http://localhost/clearskysolar/public/" style="color: black; padding: 7px 26px; 
                    background-color: #EFEFEF; text-decoration: none; border-style: solid; border-width: 3px; border-top-color: grey; 
                    border-right-color: black; border-bottom-color: black; border-left-color: grey; font-weight: bold;">Ga terug naar de website</a>
                </div>';

        try {
            $phpmailer->send();
            $this->addFlash('order_success', 'Je bestelling is succesvol geplaatst! Je ontvangt een e-mail ter bevestiging.');
            return $this->redirectToRoute('app_default', ['clearStorage' => 1]);

        } catch (Exception $e) {
            echo "Bericht kon niet worden verzonden. Mailerfout: {$phpmailer->ErrorInfo}";
        }

        return $this->redirectToRoute('app_default');
    }
}
