<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 10)]
    private ?string $ssn = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    /**
     * @var Collection<int, Loan>
     */
    #[ORM\OneToMany(targetEntity: Loan::class, mappedBy: 'customer')]
    private Collection $loans;

    /**
     * @var Collection<int, PaymentOrder>
     */
    #[ORM\OneToMany(targetEntity: PaymentOrder::class, mappedBy: 'customer')]
    private Collection $paymentOrders;

    public function __construct()
    {
        $this->loans = new ArrayCollection();
        $this->paymentOrders = new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getSsn(): ?string
    {
        return $this->ssn;
    }

    public function setSsn(string $ssn): static
    {
        $this->ssn = $ssn;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Loan>
     */
    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): static
    {
        if (!$this->loans->contains($loan)) {
            $this->loans->add($loan);
            $loan->setCustomer($this);
        }

        return $this;
    }

    public function removeLoan(Loan $loan): static
    {
        if ($this->loans->removeElement($loan)) {
            // set the owning side to null (unless already changed)
            if ($loan->getCustomer() === $this) {
                $loan->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PaymentOrder>
     */
    public function getPaymentOrders(): Collection
    {
        return $this->paymentOrders;
    }

    public function addPaymentOrder(PaymentOrder $paymentOrder): static
    {
        if (!$this->paymentOrders->contains($paymentOrder)) {
            $this->paymentOrders->add($paymentOrder);
            $paymentOrder->setCustomer($this);
        }

        return $this;
    }

    public function removePaymentOrder(PaymentOrder $paymentOrder): static
    {
        if ($this->paymentOrders->removeElement($paymentOrder)) {
            // set the owning side to null (unless already changed)
            if ($paymentOrder->getCustomer() === $this) {
                $paymentOrder->setCustomer(null);
            }
        }

        return $this;
    }
}
