<?php

namespace App\Controller;

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

class OrderHistoryReportsController extends AbstractController
{
 
    #[Route('/top-selling-medicines', name: 'top_selling_medicines')]
    public function topSelling(OrderHistoryRepository $orderHistoryRepository) : Response
    {
        $top = $orderHistoryRepository->getTopSellingMedicines();

        return $this->render('order_history/top_selling.html.twig', [
            'medicines' => $top,
        ]);
    }

    #[Route('/top_countries_in_group', name: 'top_countries_in_group')]
    public function topCountriesInGroup(OrderHistoryRepository $orderHistoryRepository) : Response
    {
        $countries = $orderHistoryRepository->getTopCountriesInGroup();

        return $this->render('order_history/top_countries_in_group.html.twig', [
            'countries' => $countries,
        ]);
    }

    #[Route('/top_sources_in_customer_status', name: 'top_sources_in_customer_status')]
    public function topSourceInMaritalStatus(OrderHistoryRepository $orderHistoryRepository) : Response
    {
        $data = $orderHistoryRepository->getTopSourcesInCustomerStatus();

        return $this->render('order_history/top_sources_in_customer_status.html.twig', [
            'customerStatuses' => $data,
        ]);
    }

    #[Route('/total_consonants', name: 'total_consonants')]
    public function totalConsonants(OrderHistoryRepository $orderHistoryRepository) : Response
    {
        $data = $orderHistoryRepository->getTotalConsonants();

        return $this->render('order_history/total_consonants.html.twig', [
            'totalConsonants' => $data,
        ]);
    }

    
}
