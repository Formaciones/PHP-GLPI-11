<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'customers')]
class Customer
{
    #[ORM\Id]
    #[ORM\Column(name: 'CustomerID', type: 'string', length: 5)]
    private ?string $customerId = null;

    #[ORM\Column(name: 'CompanyName', type: 'string', length: 40)]
    private ?string $companyName = null;

    #[ORM\Column(
        name: 'ContactName',
        type: 'string',
        length: 30,
        nullable: true
    )]
    private ?string $contactName = null;

    #[ORM\Column(
        name: 'ContactTitle',
        type: 'string',
        length: 30,
        nullable: true
    )]
    private ?string $contactTitle = null;

    #[ORM\Column(
        name: 'Address',
        type: 'string',
        length: 60,
        nullable: true
    )]
    private ?string $address = null;

    #[ORM\Column(
        name: 'City',
        type: 'string',
        length: 15,
        nullable: true
    )]
    private ?string $city = null;

    #[ORM\Column(
        name: 'Region',
        type: 'string',
        length: 15,
        nullable: true
    )]
    private ?string $region = null;

    #[ORM\Column(
        name: 'PostalCode',
        type: 'string',
        length: 10,
        nullable: true
    )]
    private ?string $postalCode = null;

    #[ORM\Column(
        name: 'Country',
        type: 'string',
        length: 15,
        nullable: true
    )]
    private ?string $country = null;

    #[ORM\Column(
        name: 'Phone',
        type: 'string',
        length: 24,
        nullable: true
    )]
    private ?string $phone = null;

    #[ORM\Column(
        name: 'Fax',
        type: 'string',
        length: 24,
        nullable: true
    )]
    private ?string $fax = null;

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function setContactName(?string $contactName): static
    {
        $this->contactName = $contactName;

        return $this;
    }

    public function getContactTitle(): ?string
    {
        return $this->contactTitle;
    }

    public function setContactTitle(?string $contactTitle): static
    {
        $this->contactTitle = $contactTitle;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }
}