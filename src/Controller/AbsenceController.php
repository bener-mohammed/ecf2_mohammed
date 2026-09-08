<?php

namespace App\Controller;

use App\Repository\AbsenceRepository;
use App\Entity\Absence;
use App\Form\AbsenceType;
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
    #[Route(
        '/admin/trainees/{id}/absence/new',
        name: 'app_absence_new',
        methods: ['GET', 'POST']
    )]
    #[Route(
        '/admin/absences',
        name: 'app_absence_index',
        methods: ['GET']
    )]
    public function index(AbsenceRepository $absenceRepository): Response
    {
        $absences = $absenceRepository->findBy(
            [],
            ['absenceDate' => 'DESC']
        );

        return $this->render('absence/index.html.twig', [
            'absences' => $absences,
        ]);
    }
    public function new(
        int $id,
        TraineeRepository $traineeRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $trainee = $traineeRepository->find($id);

        if (!$trainee) {
            throw $this->createNotFoundException('Stagiaire introuvable.');
        }

        $absence = new Absence();

        $absence->setTrainee($trainee);

        $form = $this->createForm(AbsenceType::class, $absence);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $proofFile = $form->get('proofFile')->getData();

            if ($proofFile) {
                $newFilename = bin2hex(random_bytes(16)) . '.pdf';

                $proofsDirectory =
                    $this->getParameter('kernel.project_dir')
                    . '/public/uploads/proofs';

                try {
                    $proofFile->move(
                        $proofsDirectory,
                        $newFilename
                    );

                    $absence->setProofFilename($newFilename);
                } catch (FileException $exception) {
                    $form->get('proofFile')->addError(
                        new FormError(
                            'Le justificatif n\'a pas pu être enregistré.'
                        )
                    );

                    return $this->render('absence/new.html.twig', [
                        'form' => $form->createView(),
                        'trainee' => $trainee,
                    ]);
                }
            }

            $entityManager->persist($absence);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L\'absence a bien été enregistrée.'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('absence/new.html.twig', [
            'form' => $form->createView(),
            'trainee' => $trainee,
        ]);
    }
}
