<?php

namespace App\Entity;

use App\Repository\TraineeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TraineeRepository::class)]
#[ORM\Table(name: 'trainee')]
#[ORM\UniqueConstraint(name: 'uq_trainee_afpa_id', columns: ['afpa_id'])]
#[ORM\UniqueConstraint(name: 'uq_trainee_email', columns: ['email'])]
#[UniqueEntity(
    fields: ['afpaId'],
    message: 'Cet identifiant AFPA est déjà utilisé.',
    errorPath: 'afpaId'
)]
#[UniqueEntity(
    fields: ['email'],
    message: 'Cet email est déjà utilisé.',
    errorPath: 'email'
)]
class Trainee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'trainee_id', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'afpa_id', length: 20)]
    #[Assert\NotBlank(
        message: 'L\'identifiant AFPA est obligatoire.'
    )]
    #[Assert\Length(
        max: 20,
        maxMessage: 'L\'identifiant AFPA ne peut pas dépasser 20 caractères.'
    )]
    private ?string $afpaId = null;

    #[ORM\Column(name: 'first_name', length: 100)]
    #[Assert\NotBlank(
        message: 'Le prénom est obligatoire.'
    )]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le prénom ne peut pas dépasser 100 caractères.'
    )]
    private ?string $firstName = null;

    #[ORM\Column(name: 'last_name', length: 100)]
    #[Assert\NotBlank(
        message: 'Le nom est obligatoire.'
    )]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le nom ne peut pas dépasser 100 caractères.'
    )]
    private ?string $lastName = null;

    #[ORM\Column(name: 'email', length: 180)]
    #[Assert\NotBlank(
        message: 'L\'email est obligatoire.'
    )]
    #[Assert\Email(
        message: 'Veuillez saisir une adresse email valide.'
    )]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'email ne peut pas dépasser 180 caractères.'
    )]
    private ?string $email = null;

    #[ORM\Column(name: 'phone', length: 20)]
    #[Assert\NotBlank(
        message: 'Le numéro de téléphone est obligatoire.'
    )]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Le numéro de téléphone ne peut pas dépasser 20 caractères.'
    )]
    private ?string $phone = null;

    #[ORM\Column(name: 'residence', length: 100, nullable: true)]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le lieu de résidence ne peut pas dépasser 100 caractères.'
    )]
    private ?string $residence = null;

    #[ORM\Column(name: 'birth_date', type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(
        message: 'La date de naissance est obligatoire.'
    )]
    #[Assert\LessThan(
        'today',
        message: 'La date de naissance doit être antérieure à aujourd\'hui.'
    )]
    private ?\DateTimeImmutable $birthDate = null;

    #[ORM\Column(name: 'photo_filename', length: 255, nullable: true)]
    private ?string $photoFilename = null;

    /**
     * @var Collection<int, Absence>
     */
    #[ORM\OneToMany(targetEntity: Absence::class, mappedBy: 'trainee')]
    private Collection $absences;

    public function __construct()
    {
        $this->absences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAfpaId(): ?string
    {
        return $this->afpaId;
    }

    public function setAfpaId(string $afpaId): static
    {
        $this->afpaId = $afpaId;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getResidence(): ?string
    {
        return $this->residence;
    }

    public function setResidence(?string $residence): static
    {
        $this->residence = $residence;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getPhotoFilename(): ?string
    {
        return $this->photoFilename;
    }

    public function setPhotoFilename(?string $photoFilename): static
    {
        $this->photoFilename = $photoFilename;

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setTrainee($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getTrainee() === $this) {
                $absence->setTrainee(null);
            }
        }

        return $this;
    }
}
