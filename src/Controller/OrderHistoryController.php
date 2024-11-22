<?php

namespace App\Controller;

use App\Core\Importer\FileImporterFactory;
use App\Core\Importer\JsonFileImporter;
use App\Core\Importer\LdifFileImporter;
use App\Entity\OrderHistory;
use App\Repository\OrderHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Core\Importer\CsvFileImporter;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderHistoryController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(OrderHistoryRepository $orderHistoryRepository) : Response
    {
        $totalCount = $orderHistoryRepository->getTotalCount();

        return $this->render('order_history/index.html.twig', [
            'totalCount' => $totalCount,
        ]);
    }

    #[Route('/order_history/store', name: 'order_history_store', methods: ['POST'])]
    public function store(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        FileImporterFactory $fileImporterFactory) : Response
    {
        $rowsParsed = 0;

        $file = $request->files->get('history_file');
        $fileExtension = $file->getClientOriginalExtension();

        if (! $file || ! in_array($fileExtension, ['csv', 'json', 'ldif'])) {
            $this->addFlash('error', 'Nieprawidłowy format pliku.');
            return $this->redirectToRoute('home');
        }

        try {
            $fileImporter = $fileImporterFactory->createFileImporter($file);
        } catch (Exception $exception) {
            $this->addFlash('error', 'Wystąpił błąd podczas odczytu pliku');
            return $this->redirectToRoute("home");
        }

        $rowsParsed = 0;
        $errorsCounter = 0;

        while ($data = $fileImporter->getNextRecord()) {
            if ($data == null)
                break;

            $entity = new OrderHistory();
            $entity->setCustomer($data['Customer'] ?? null);
            $entity->setCountry($data['Country'] ?? null);
            $entity->setProduct($data['Order'] ?? null);
            $entity->setCustomerStatus($data['Status'] ?? null);
            $entity->setCustomerGroup($data['Group'] ?? null);
            $entity->setSource($fileImporter->getFileFormat());

            $errors = $validator->validate($entity);
            if (count($errors) > 0) {
                $errorsCounter++;
                //log $errors
                continue;
            }

            $entityManager->persist($entity);

            $rowsParsed++;
            if ($rowsParsed % 1000 == 0) {
                $entityManager->flush();
            }
        }

        $entityManager->flush();

        if ($errorsCounter == 0) {
            $this->addFlash('success', 'Dane załadowane do bazy. ' . ($rowsParsed-1) . ' rekordow');
        } else {
            $this->addFlash('error', 'Dane załadowane do bazy. Rekordy z błędami pominięto.');
        }
        return $this->redirectToRoute("home");
    }
}
