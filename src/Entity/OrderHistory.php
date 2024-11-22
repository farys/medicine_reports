<?php

namespace App\Entity;

use App\Repository\OrderHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderHistoryRepository::class)]
#[ORM\Table("order_history", indexes: [
    new ORM\Index(name: "idx_customer_group", columns: ["customer_group"]), 
    new ORM\Index(name: "idx_customer_status", columns: ["customer_status"]),
    ])]
class OrderHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $customer = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    //#[Assert\Country]
    private ?string $country = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $product = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    #[Assert\Choice(['Single', 'Common-Law', 'Divorced', 'Married'])]
    private ?string $customerStatus = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\PositiveOrZero]
    private ?int $customerGroup = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 4)]
    private ?string $source = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(?string $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getCustomerStatus(): ?string
    {
        return $this->customerStatus;
    }

    public function setCustomerStatus(?string $customerStatus): static
    {
        $this->customerStatus = $customerStatus;

        return $this;
    }

    public function getCustomerGroup(): ?int
    {
        return $this->customerGroup;
    }

    public function setCustomerGroup(?int $customerGroup): static
    {
        $this->customerGroup = $customerGroup;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }
}
