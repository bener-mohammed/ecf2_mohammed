<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Entity\Trainee;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\TraineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AbsenceController extends AbstractController
{
    /*
     * READ
     * Display all absences.
     */
    #[Route(
        '/admin/absences',
        name: 'app_absence_index',
        methods: ['GET']
    )]
    public function index(
        AbsenceRepository $absenceRepository,
        TraineeRepository $traineeRepository
    ): Response {
        $absences = $absenceRepository->findBy(
            [],
            ['absenceDate' => 'DESC']
        );

        $statistics = $traineeRepository
            ->getAbsenceStatistics();

        $statisticsByTrainee = [];

        foreach ($statistics as $statistic) {
            $statisticsByTrainee[$statistic['traineeId']] = $statistic;
        }

        return $this->render('absence/index.html.twig', [
            'absences' => $absences,
            'statisticsByTrainee' => $statisticsByTrainee,
        ]);
    }

    /*
     * CREATE
     * Create a new absence for a specific trainee.
     */
    #[Route(
        '/admin/trainees/{id}/absence/new',
        name: 'app_absence_new',
        methods: ['GET', 'POST']
    )]
    public function new(
        int $id,
        TraineeRepository $traineeRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $trainee = $traineeRepository->find($id);

        if (!$trainee) {
            throw $this->createNotFoundException(
                'Stagiaire introuvable.'
            );
        }

        $absence = new Absence();

        $absence->setTrainee($trainee);

        $form = $this->createForm(
            AbsenceType::class,
            $absence
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $proofFile = $form
                ->get('proofFile')
                ->getData();

            if ($proofFile) {
                $newFilename =
                    bin2hex(random_bytes(16)) . '.pdf';

                $proofsDirectory =
                    $this->getParameter('kernel.project_dir')
                    . '/public/uploads/proofs';

                try {
                    $proofFile->move(
                        $proofsDirectory,
                        $newFilename
                    );

                    $absence->setProofFilename(
                        $newFilename
                    );
                } catch (FileException $exception) {
                    $form
                        ->get('proofFile')
                        ->addError(
                            new FormError(
                                'Le justificatif n\'a pas pu être enregistré.'
                            )
                        );

                    return $this->render(
                        'absence/new.html.twig',
                        [
                            'form' => $form->createView(),
                            'trainee' => $trainee,
                        ]
                    );
                }
            }

            $entityManager->persist($absence);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L\'absence a bien été enregistrée.'
            );

            return $this->redirectToRoute(
                'app_home'
            );
        }

        return $this->render(
            'absence/new.html.twig',
            [
                'form' => $form->createView(),
                'trainee' => $trainee,
            ]
        );
    }

    /*
     * UPDATE
     * Edit an existing absence.
     */
    #[Route(
        '/admin/absences/{id}/edit',
        name: 'app_absence_edit',
        methods: ['GET', 'POST']
    )]
    public function edit(
        int $id,
        AbsenceRepository $absenceRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $absence = $absenceRepository->find($id);

        if (!$absence) {
            throw $this->createNotFoundException(
                'Absence introuvable.'
            );
        }

        $oldProofFilename =
            $absence->getProofFilename();

        $form = $this->createForm(
            AbsenceType::class,
            $absence
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $proofFile = $form
                ->get('proofFile')
                ->getData();

            if ($proofFile) {
                $newFilename =
                    bin2hex(random_bytes(16)) . '.pdf';

                $proofsDirectory =
                    $this->getParameter('kernel.project_dir')
                    . '/public/uploads/proofs';

                try {
                    $proofFile->move(
                        $proofsDirectory,
                        $newFilename
                    );

                    $absence->setProofFilename(
                        $newFilename
                    );

                    if ($oldProofFilename) {
                        $oldProofPath =
                            $proofsDirectory
                            . '/'
                            . $oldProofFilename;

                        if (is_file($oldProofPath)) {
                            unlink($oldProofPath);
                        }
                    }
                } catch (FileException $exception) {
                    $form
                        ->get('proofFile')
                        ->addError(
                            new FormError(
                                'Le nouveau justificatif n\'a pas pu être enregistré.'
                            )
                        );

                    return $this->render(
                        'absence/edit.html.twig',
                        [
                            'form' => $form->createView(),
                            'absence' => $absence,
                        ]
                    );
                }
            }

            $entityManager->flush();

            $this->addFlash(
                'success',
                'L\'absence a bien été modifiée.'
            );

            return $this->redirectToRoute(
                'app_absence_index'
            );
        }

        return $this->render(
            'absence/edit.html.twig',
            [
                'form' => $form->createView(),
                'absence' => $absence,
            ]
        );
    }
    #[Route(
        '/admin/absences/{id}/delete',
        name: 'app_absence_delete',
        methods: ['POST']
    )]
    public function delete(
        int $id,
        AbsenceRepository $absenceRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $absence = $absenceRepository->find($id);

        if (!$absence) {
            throw $this->createNotFoundException(
                'Absence introuvable.'
            );
        }

        if (!$this->isCsrfTokenValid(
            'delete' . $absence->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        $proofFilename = $absence->getProofFilename();

        $entityManager->remove($absence);
        $entityManager->flush();

        if ($proofFilename) {
            $proofPath =
                $this->getParameter('kernel.project_dir')
                . '/public/uploads/proofs/'
                . $proofFilename;

            if (is_file($proofPath)) {
                unlink($proofPath);
            }
        }

        $this->addFlash(
            'success',
            'L\'absence a bien été supprimée.'
        );

        return $this->redirectToRoute(
            'app_absence_index'
        );
    }
}
