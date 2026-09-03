<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
#[ORM\Table(name: 'absence')]
#[ORM\UniqueConstraint(
    name: 'uq_absence_trainee_date',
    columns: ['trainee_id', 'absence_date']
)]
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'absence_id', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'absence_date', type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $absenceDate = null;

    #[ORM\Column(name: 'reason', length: 30)]
    private ?string $reason = null;

    #[ORM\Column(name: 'proof_filename', length: 255, nullable: true)]
    private ?string $proofFilename = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    #[ORM\JoinColumn(
        name: 'trainee_id',
        referencedColumnName: 'trainee_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    private ?Trainee $trainee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbsenceDate(): ?\DateTimeImmutable
    {
        return $this->absenceDate;
    }

    public function setAbsenceDate(\DateTimeImmutable $absenceDate): static
    {
        $this->absenceDate = $absenceDate;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getProofFilename(): ?string
    {
        return $this->proofFilename;
    }

    public function setProofFilename(?string $proofFilename): static
    {
        $this->proofFilename = $proofFilename;

        return $this;
    }

    public function getTrainee(): ?Trainee
    {
        return $this->trainee;
    }

    public function setTrainee(?Trainee $trainee): static
    {
        $this->trainee = $trainee;

        return $this;
    }
}