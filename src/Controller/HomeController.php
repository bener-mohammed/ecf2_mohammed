<?php

namespace App\Controller;

use App\Repository\TraineeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        TraineeRepository $traineeRepository
    ): Response {
        $trainees = $traineeRepository->findAll();

        $statistics = $traineeRepository
            ->getAbsenceStatistics();

        $unexcusedCounts = [];

        foreach ($statistics as &$statistic) {
            $statistic['lostIncome'] =
                $statistic['totalAbsences'] * (712 / 21);

            $unexcusedCounts[$statistic['traineeId']] =
                (int) $statistic['unexcusedAbsences'];
        }

        unset($statistic);

        return $this->render('home/index.html.twig', [
            'trainees' => $trainees,
            'statistics' => $statistics,
            'unexcusedCounts' => $unexcusedCounts,
        ]);
    }
}
