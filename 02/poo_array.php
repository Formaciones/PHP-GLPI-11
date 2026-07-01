<?php
	declare(strict_types=1);

	$page = "poo1";

	// Require de datos conservado como referencia de plantilla.
	require_once __DIR__ . '/data.php';

    final class ProductRepositiry
    {
        private array $productosPorId = [];

        public function __construct(array $productos)
        {
            foreach($productos as $producto)
            {
                if(!isset($producto['productId'])) continue;
                $id = (int) $producto['productId'];
                $this->productosPorId[$id] = $producto;
            }
        }

        public function findByProductId(int $productId) : ?array {
            return $this->productosPorId[$productId] ?? null;
        }

        public function getAllProductIds() : array
        {
            $ids = array_keys($this->productosPorId);
            sort($ids);
            return $ids;
        }
    }

    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    $productRepository = new ProductRepositiry($productos);
    $productIdInput = '';
    $productForm = [
        'productId' => '',
        'productName' => '',
        'supplierId' => '',
        'categoryId' => '',
        'quantityPerUnit' => '',
        'unitPrice' => '',
        'unitsInStock' => '',
        'unitsOnOrder' => '',
        'reorderLevel' => '',
        'discontinued' =>'',
    ];

    $idsDisponibles = $productRepository->getAllProductIds();

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $productIdInput = trim($_POST['productId'] ?? '');
        $productoEncontrado = $productRepository->findByProductId((int) $productIdInput);

        $productForm = $productoEncontrado;
        $productForm['discontinued'] = $productForm['discontinued'] === 1 ? 'Si' : 'No';
    }
?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PHP + GLPI 11 | POO</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="/labs/css/style.css" rel="stylesheet">
</head>
<body>
	<div class="layout d-flex">
		<?php
			include_once __DIR__ . '/../layout/aside.php';
		?>

		<main class="flex-grow-1 p-4 p-lg-5">
			<div class="container-fluid">
				<header class="mb-4">
					<h1 class="display-6 fw-bold mb-3">PHP Programación Orientada a Objetos (POO)</h1>
					<hr class="border-secondary-subtle opacity-100">
				</header>

				<section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
					<div class="text-center mx-auto" style="max-width: 960px;">
						<h2 class="h4 mb-4">Ejemplo POO con data.php como base de datos</h2>

						<p class="text-start">
							Plantilla HTML base para el ejercicio de busqueda por <strong>productId</strong>.
						</p>

						<form method="post" action="" class="text-start border rounded p-4 bg-light-subtle mb-4">
							<div class="mb-3">
								<label for="productId" class="form-label">Introduce productId</label>
								<input
									type="number"
									class="form-control"
									id="productId"
									name="productId"
									value=""
									min="1"
									placeholder="Ejemplo: 1"
								>
								<div class="form-text">IDs disponibles: <?php echo e(implode(', ', array_slice($idsDisponibles, 0, 15))); ?>...</div>
							</div>

							<button type="submit" class="btn btn-primary">Cargar producto</button>
						</form>

						<h3 class="h5 text-start mb-3">Datos cargados en el formulario</h3>

						<form class="text-start border rounded p-4">
							<div class="row g-3">
								<div class="col-md-3">
									<label class="form-label" for="formProductId"><b>ID Producto</b></label>
									<input id="formProductId" type="text" class="form-control" value="<?= $productForm['productId'] ?>" readonly>
								</div>
								<div class="col-md-9">
									<label class="form-label" for="formProductName"><b>Nombre del Producto</b></label>
									<input id="formProductName" type="text" class="form-control" value="<?= $productForm['productName'] ?>" readonly>
								</div>

								<div class="col-md-3">
									<label class="form-label" for="formSupplierId"><b>ID Proveedor</b></label>
									<input id="formSupplierId" type="text" class="form-control" value="<?= $productForm['supplierId'] ?>" readonly>
								</div>
								<div class="col-md-3">
									<label class="form-label" for="formCategoryId"><b>ID Categoría</b></label>
									<input id="formCategoryId" type="text" class="form-control" value="<?= $productForm['categoryId'] ?>" readonly>
								</div>
								<div class="col-md-6">
									<label class="form-label" for="formQuantityPerUnit"><b>Cantidad por Unidad</b></label>
									<input id="formQuantityPerUnit" type="text" class="form-control" value="<?= $productForm['quantityPerUnit'] ?>" readonly>
								</div>

								<div class="col-md-3">
									<label class="form-label" for="formUnitPrice"><b>Precio Unitario</b></label>
									<input id="formUnitPrice" type="text" class="form-control" value="<?= $productForm['unitPrice'] ?>" readonly>
								</div>
								<div class="col-md-3">
									<label class="form-label" for="formUnitsInStock"><b>Unidades en Stock</b></label>
									<input id="formUnitsInStock" type="text" class="form-control" value="<?= $productForm['unitsInStock'] ?>" readonly>
								</div>
								<div class="col-md-3">
									<label class="form-label" for="formUnitsOnOrder"><b>Unidades en Pedido</b></label>
									<input id="formUnitsOnOrder" type="text" class="form-control" value="<?= $productForm['unitsOnOrder'] ?>" readonly>
								</div>
								<div class="col-md-3">
									<label class="form-label" for="formReorderLevel"><b>Nivel de Reorden</b></label>
									<input id="formReorderLevel" type="text" class="form-control" value="<?= $productForm['reorderLevel'] ?>" readonly>
								</div>

								<div class="col-md-3">
									<label class="form-label" for="formDiscontinued"><b>Descontinuado</b></label>
									<input id="formDiscontinued" type="text" class="form-control" value="<?= $productForm['discontinued'] ?>" readonly>
								</div>
							</div>
						</form>
					</div>
				</section>
			</div>
		</main>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
