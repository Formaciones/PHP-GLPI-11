<?php
declare(strict_types=1);

/**
 * Entidad Customer alineada con la tabla customers de Northwind.
 */
final class Customer
{
	private string $customerId;
	private string $companyName;
	private ?string $contactName;
	private ?string $contactTitle;
	private ?string $address;
	private ?string $city;
	private ?string $region;
	private ?string $postalCode;
	private ?string $country;
	private ?string $phone;
	private ?string $fax;

	public function __construct(
		string $customerId,
		string $companyName,
		?string $contactName = null,
		?string $contactTitle = null,
		?string $address = null,
		?string $city = null,
		?string $region = null,
		?string $postalCode = null,
		?string $country = null,
		?string $phone = null,
		?string $fax = null
	) {
		$this->customerId = $customerId;
		$this->companyName = $companyName;
		$this->contactName = $contactName;
		$this->contactTitle = $contactTitle;
		$this->address = $address;
		$this->city = $city;
		$this->region = $region;
		$this->postalCode = $postalCode;
		$this->country = $country;
		$this->phone = $phone;
		$this->fax = $fax;
	}

	public function getCustomerId(): string
	{
		return $this->customerId;
	}

	public function setCustomerId(string $customerId): void
	{
		$this->customerId = $customerId;
	}

	public function getCompanyName(): string
	{
		return $this->companyName;
	}

	public function setCompanyName(string $companyName): void
	{
		$this->companyName = $companyName;
	}

	public function getContactName(): ?string
	{
		return $this->contactName;
	}

	public function setContactName(?string $contactName): void
	{
		$this->contactName = $contactName;
	}

	public function getContactTitle(): ?string
	{
		return $this->contactTitle;
	}

	public function setContactTitle(?string $contactTitle): void
	{
		$this->contactTitle = $contactTitle;
	}

	public function getAddress(): ?string
	{
		return $this->address;
	}

	public function setAddress(?string $address): void
	{
		$this->address = $address;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	public function setCity(?string $city): void
	{
		$this->city = $city;
	}

	public function getRegion(): ?string
	{
		return $this->region;
	}

	public function setRegion(?string $region): void
	{
		$this->region = $region;
	}

	public function getPostalCode(): ?string
	{
		return $this->postalCode;
	}

	public function setPostalCode(?string $postalCode): void
	{
		$this->postalCode = $postalCode;
	}

	public function getCountry(): ?string
	{
		return $this->country;
	}

	public function setCountry(?string $country): void
	{
		$this->country = $country;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): void
	{
		$this->phone = $phone;
	}

	public function getFax(): ?string
	{
		return $this->fax;
	}

	public function setFax(?string $fax): void
	{
		$this->fax = $fax;
	}

	/**
	 * Crea un Customer desde un array con claves SQL (CustomerID...) o camelCase.
	 *
	 * @param array<string, mixed> $data
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			(string) ($data['CustomerID'] ?? $data['customerId'] ?? ''),
			(string) ($data['CompanyName'] ?? $data['companyName'] ?? ''),
			isset($data['ContactName']) ? (string) $data['ContactName'] : (isset($data['contactName']) ? (string) $data['contactName'] : null),
			isset($data['ContactTitle']) ? (string) $data['ContactTitle'] : (isset($data['contactTitle']) ? (string) $data['contactTitle'] : null),
			isset($data['Address']) ? (string) $data['Address'] : (isset($data['address']) ? (string) $data['address'] : null),
			isset($data['City']) ? (string) $data['City'] : (isset($data['city']) ? (string) $data['city'] : null),
			isset($data['Region']) ? (string) $data['Region'] : (isset($data['region']) ? (string) $data['region'] : null),
			isset($data['PostalCode']) ? (string) $data['PostalCode'] : (isset($data['postalCode']) ? (string) $data['postalCode'] : null),
			isset($data['Country']) ? (string) $data['Country'] : (isset($data['country']) ? (string) $data['country'] : null),
			isset($data['Phone']) ? (string) $data['Phone'] : (isset($data['phone']) ? (string) $data['phone'] : null),
			isset($data['Fax']) ? (string) $data['Fax'] : (isset($data['fax']) ? (string) $data['fax'] : null)
		);
	}

	/**
	 * Exporta la entidad con nombres de columna SQL.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return [
			'CustomerID' => $this->customerId,
			'CompanyName' => $this->companyName,
			'ContactName' => $this->contactName,
			'ContactTitle' => $this->contactTitle,
			'Address' => $this->address,
			'City' => $this->city,
			'Region' => $this->region,
			'PostalCode' => $this->postalCode,
			'Country' => $this->country,
			'Phone' => $this->phone,
			'Fax' => $this->fax,
		];
	}
}
