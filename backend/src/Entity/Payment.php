<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private ?\DateTimeImmutable $payment_date = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?Loan $loans = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_assigned = null;

    #[ORM\OneToOne(mappedBy: 'payment', cascade: ['persist', 'remove'])]
    private ?PaymentOrder $paymentOrder = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeImmutable $payment_date): static
    {
        $this->payment_date = $payment_date;
        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getLoans(): ?Loan
    {
        return $this->loans;
    }

    public function setLoans(?Loan $loans): static
    {
        $this->loans = $loans;

        return $this;
    }

    public function isAssigned(): ?bool
    {
        return $this->is_assigned;
    }

    public function setIsAssigned(?bool $is_assigned): static
    {
        $this->is_assigned = $is_assigned;

        return $this;
    }

    public function getPaymentOrder(): ?PaymentOrder
    {
        return $this->paymentOrder;
    }

    public function setPaymentOrder(PaymentOrder $paymentOrder): static
    {
        // set the owning side of the relation if necessary
        if ($paymentOrder->getPayment() !== $this) {
            $paymentOrder->setPayment($this);
        }

        $this->paymentOrder = $paymentOrder;

        return $this;
    }
}

