<?php

namespace App\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Trainee;
use App\Form\TraineeType;
use App\Repository\TraineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/trainees')]
final class TraineeController extends AbstractController
{
    #[Route(name: 'app_trainee_index', methods: ['GET'])]
    public function index(
        TraineeRepository $traineeRepository
    ): Response {
        $trainees = $traineeRepository->findAll();

        $statistics = $traineeRepository
            ->getAbsenceStatistics();

        $unexcusedCounts = [];

        foreach ($statistics as $statistic) {
            $unexcusedCounts[$statistic['traineeId']] =
                (int) $statistic['unexcusedAbsences'];
        }

        return $this->render('trainee/index.html.twig', [
            'trainees' => $trainees,
            'unexcusedCounts' => $unexcusedCounts,
        ]);
    }

    #[Route('/new', name: 'app_trainee_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $trainee = new Trainee();

        $form = $this->createForm(
            TraineeType::class,
            $trainee
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form
                ->get('photoFile')
                ->getData();

            if ($photoFile) {
                $extension =
                    $photoFile->guessExtension() ?: 'jpg';

                $newFilename =
                    bin2hex(random_bytes(16))
                    . '.'
                    . $extension;

                $photosDirectory =
                    $this->getParameter('kernel.project_dir')
                    . '/public/uploads/trainees';

                try {
                    $photoFile->move(
                        $photosDirectory,
                        $newFilename
                    );

                    $trainee->setPhotoFilename(
                        $newFilename
                    );
                } catch (FileException $exception) {
                    $form
                        ->get('photoFile')
                        ->addError(
                            new FormError(
                                'La photo n\'a pas pu être enregistrée.'
                            )
                        );

                    return $this->render(
                        'trainee/new.html.twig',
                        [
                            'trainee' => $trainee,
                            'form' => $form,
                        ]
                    );
                }
            }

            $entityManager->persist($trainee);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le stagiaire a bien été ajouté.'
            );

            return $this->redirectToRoute(
                'app_trainee_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(
            'trainee/new.html.twig',
            [
                'trainee' => $trainee,
                'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: 'app_trainee_show', methods: ['GET'])]
    public function show(Trainee $trainee): Response
    {
        return $this->render('trainee/show.html.twig', [
            'trainee' => $trainee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trainee_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Trainee $trainee,
        EntityManagerInterface $entityManager
    ): Response {
        $oldPhotoFilename = $trainee->getPhotoFilename();

        $form = $this->createForm(
            TraineeType::class,
            $trainee
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form
                ->get('photoFile')
                ->getData();

            $newFilename = null;
            $photosDirectory =
                $this->getParameter('kernel.project_dir')
                . '/public/uploads/trainees';

            if ($photoFile) {
                $extension =
                    $photoFile->guessExtension() ?: 'jpg';

                $newFilename =
                    bin2hex(random_bytes(16))
                    . '.'
                    . $extension;

                try {
                    $photoFile->move(
                        $photosDirectory,
                        $newFilename
                    );

                    $trainee->setPhotoFilename(
                        $newFilename
                    );
                } catch (FileException $exception) {
                    $form
                        ->get('photoFile')
                        ->addError(
                            new FormError(
                                'La nouvelle photo n\'a pas pu être enregistrée.'
                            )
                        );

                    return $this->render(
                        'trainee/edit.html.twig',
                        [
                            'trainee' => $trainee,
                            'form' => $form,
                        ]
                    );
                }
            }

            $entityManager->flush();

            if ($newFilename && $oldPhotoFilename) {
                $oldPhotoPath =
                    $photosDirectory
                    . '/'
                    . $oldPhotoFilename;

                if (is_file($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            $this->addFlash(
                'success',
                'Le stagiaire a bien été modifié.'
            );

            return $this->redirectToRoute(
                'app_trainee_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render(
            'trainee/edit.html.twig',
            [
                'trainee' => $trainee,
                'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: 'app_trainee_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Trainee $trainee,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid(
            'delete' . $trainee->getId(),
            $request->getPayload()->getString('_token')
        )) {
            throw $this->createAccessDeniedException(
                'Jeton CSRF invalide.'
            );
        }

        $photoFilename = $trainee->getPhotoFilename();

        $proofFilenames = [];

        foreach ($trainee->getAbsences() as $absence) {
            if ($absence->getProofFilename()) {
                $proofFilenames[] =
                    $absence->getProofFilename();
            }
        }

        $entityManager->remove($trainee);
        $entityManager->flush();

        if ($photoFilename) {
            $photoPath =
                $this->getParameter('kernel.project_dir')
                . '/public/uploads/trainees/'
                . $photoFilename;

            if (is_file($photoPath)) {
                unlink($photoPath);
            }
        }

        $proofsDirectory =
            $this->getParameter('kernel.project_dir')
            . '/public/uploads/proofs';

        foreach ($proofFilenames as $proofFilename) {
            $proofPath =
                $proofsDirectory
                . '/'
                . $proofFilename;

            if (is_file($proofPath)) {
                unlink($proofPath);
            }
        }

        $this->addFlash(
            'success',
            'Le stagiaire a bien été supprimé.'
        );

        return $this->redirectToRoute(
            'app_trainee_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
