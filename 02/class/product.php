<?php
declare(strict_types=1);

/**
 * Entidad Product alineada con la tabla products de Northwind.
 */
final class Product
{
	private ?int $productId;
	private string $productName;
	private ?int $supplierId;
	private ?int $categoryId;
	private ?string $quantityPerUnit;
	private ?float $unitPrice;
	private ?int $unitsInStock;
	private ?int $unitsOnOrder;
	private ?int $reorderLevel;
	private bool $discontinued;

	public function __construct(
		?int $productId,
		string $productName,
		?int $supplierId = null,
		?int $categoryId = null,
		?string $quantityPerUnit = null,
		?float $unitPrice = 0.0,
		?int $unitsInStock = 0,
		?int $unitsOnOrder = 0,
		?int $reorderLevel = 0,
		bool $discontinued = false
	) {
		$this->productId = $productId;
		$this->productName = $productName;
		$this->supplierId = $supplierId;
		$this->categoryId = $categoryId;
		$this->quantityPerUnit = $quantityPerUnit;
		$this->unitPrice = $unitPrice;
		$this->unitsInStock = $unitsInStock;
		$this->unitsOnOrder = $unitsOnOrder;
		$this->reorderLevel = $reorderLevel;
		$this->discontinued = $discontinued;
	}

	public function getProductId(): ?int
	{
		return $this->productId;
	}

	public function setProductId(?int $productId): void
	{
		$this->productId = $productId;
	}

	public function getProductName(): string
	{
		return $this->productName;
	}

	public function setProductName(string $productName): void
	{
		$this->productName = $productName;
	}

	public function getSupplierId(): ?int
	{
		return $this->supplierId;
	}

	public function setSupplierId(?int $supplierId): void
	{
		$this->supplierId = $supplierId;
	}

	public function getCategoryId(): ?int
	{
		return $this->categoryId;
	}

	public function setCategoryId(?int $categoryId): void
	{
		$this->categoryId = $categoryId;
	}

	public function getQuantityPerUnit(): ?string
	{
		return $this->quantityPerUnit;
	}

	public function setQuantityPerUnit(?string $quantityPerUnit): void
	{
		$this->quantityPerUnit = $quantityPerUnit;
	}

	public function getUnitPrice(): ?float
	{
		return $this->unitPrice;
	}

	public function setUnitPrice(?float $unitPrice): void
	{
		$this->unitPrice = $unitPrice;
	}

	public function getUnitsInStock(): ?int
	{
		return $this->unitsInStock;
	}

	public function setUnitsInStock(?int $unitsInStock): void
	{
		$this->unitsInStock = $unitsInStock;
	}

	public function getUnitsOnOrder(): ?int
	{
		return $this->unitsOnOrder;
	}

	public function setUnitsOnOrder(?int $unitsOnOrder): void
	{
		$this->unitsOnOrder = $unitsOnOrder;
	}

	public function getReorderLevel(): ?int
	{
		return $this->reorderLevel;
	}

	public function setReorderLevel(?int $reorderLevel): void
	{
		$this->reorderLevel = $reorderLevel;
	}

	public function isDiscontinued(): bool
	{
		return $this->discontinued;
	}

	public function setDiscontinued(bool $discontinued): void
	{
		$this->discontinued = $discontinued;
	}

	/**
	 * Crea un Product desde un array con claves de BD (ProductID...) o camelCase.
	 *
	 * @param array<string, mixed> $data
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			isset($data['ProductID']) ? (int) $data['ProductID'] : (isset($data['productId']) ? (int) $data['productId'] : null),
			(string) ($data['ProductName'] ?? $data['productName'] ?? ''),
			isset($data['SupplierID']) ? (int) $data['SupplierID'] : (isset($data['supplierId']) ? (int) $data['supplierId'] : null),
			isset($data['CategoryID']) ? (int) $data['CategoryID'] : (isset($data['categoryId']) ? (int) $data['categoryId'] : null),
			isset($data['QuantityPerUnit']) ? (string) $data['QuantityPerUnit'] : (isset($data['quantityPerUnit']) ? (string) $data['quantityPerUnit'] : null),
			isset($data['UnitPrice']) ? (float) $data['UnitPrice'] : (isset($data['unitPrice']) ? (float) $data['unitPrice'] : 0.0),
			isset($data['UnitsInStock']) ? (int) $data['UnitsInStock'] : (isset($data['unitsInStock']) ? (int) $data['unitsInStock'] : 0),
			isset($data['UnitsOnOrder']) ? (int) $data['UnitsOnOrder'] : (isset($data['unitsOnOrder']) ? (int) $data['unitsOnOrder'] : 0),
			isset($data['ReorderLevel']) ? (int) $data['ReorderLevel'] : (isset($data['reorderLevel']) ? (int) $data['reorderLevel'] : 0),
			isset($data['Discontinued']) ? ((int) $data['Discontinued'] === 1) : (isset($data['discontinued']) ? ((int) $data['discontinued'] === 1) : false)
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
			'ProductID' => $this->productId,
			'ProductName' => $this->productName,
			'SupplierID' => $this->supplierId,
			'CategoryID' => $this->categoryId,
			'QuantityPerUnit' => $this->quantityPerUnit,
			'UnitPrice' => $this->unitPrice,
			'UnitsInStock' => $this->unitsInStock,
			'UnitsOnOrder' => $this->unitsOnOrder,
			'ReorderLevel' => $this->reorderLevel,
			'Discontinued' => $this->discontinued ? 1 : 0,
		];
	}
}
