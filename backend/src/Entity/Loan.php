<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column]
    private ?int $amount_issued = null;

    #[ORM\Column]
    private ?int $amount_to_pay = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'loans')]
    private Collection $payments;

    #[ORM\Column(length: 10)]
    private ?string $loan_number = null;

    #[ORM\Column]
    private ?bool $is_paid = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
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

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setLoans($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getLoans() === $this) {
                $payment->setLoans(null);
            }
        }

        return $this;
    }

    public function getLoanNumber(): ?string
    {
        return $this->loan_number;
    }

    public function setLoanNumber(string $loan_number): static
    {
        $this->loan_number = $loan_number;

        return $this;
    }

    public function isPaid(): ?bool
    {
        return $this->is_paid;
    }

    public function setIsPaid(bool $is_paid): static
    {
        $this->is_paid = $is_paid;

        return $this;
    }
}
