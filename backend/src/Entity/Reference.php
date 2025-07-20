<?php

namespace App\Entity;

use App\Repository\ReferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReferenceRepository::class)]
class Reference
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\OneToOne(mappedBy: 'reference', cascade: ['persist', 'remove'])]
    private ?Loan $loan = null;

    public function __construct()
    {
        if ($this->id === null) {
            $this->id = Uuid::v4()->toRfc4122();
        }
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getLoan(): ?Loan
    {
        return $this->loan;
    }

    public function setLoan(Loan $loan): static
    {
        // set the owning side of the relation if necessary
        if ($loan->getReference() !== $this) {
            $loan->setReference($this);
        }

        $this->loan = $loan;

        return $this;
    }
}
