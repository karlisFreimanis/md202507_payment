<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\OneToOne(inversedBy: 'loan', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reference $reference = null;

    #[ORM\Column]
    private ?int $amount_issued = null;

    #[ORM\Column]
    private ?int $amount_to_pay = null;

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getReference(): ?Reference
    {
        return $this->reference;
    }

    public function setReference(Reference $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAmountIssued(): ?int
    {
        return $this->amount_issued;
    }

    public function setAmountIssued(int $amount_issued): static
    {
        $this->amount_issued = $amount_issued;

        return $this;
    }

    public function getAmountToPay(): ?int
    {
        return $this->amount_to_pay;
    }

    public function setAmountToPay(int $amount_to_pay): static
    {
        $this->amount_to_pay = $amount_to_pay;

        return $this;
    }
}
