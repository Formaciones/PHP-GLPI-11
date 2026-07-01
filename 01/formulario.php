<?php 
    $page = 'form';

    // Habilita la sesión para la conexión o usuario
    // Permite intercambiar información entre la diferentes páginas de una misma conexión
    // Inactivida máxima de la sesión 1440 seg. (24 min)
    session_start();

    // Crear colección de registro si no existe
    // Crear como variable de sesisón
    if(!isset($_SESSION['registros'])) {
        $_SESSION['registros'] = [];
    }

    //$_SESSION['registros'] = [];

    // Leer de las Cookies si estamos en mode Insertar o Edición
    $modoCookie = $_COOKIE['modo_formulario'] ?? 'insertar';

    // Leer un parámetro de las URL
    $accionGet = $_GET['accion'] ?? '';

    // Leer campos del formulario
    $idGet = isset($_GET['id']) ? (int) $_GET['id'] : null;
    $mensaje = '';
    $errores = [];

    // Array -> Colección o lista de valores
    $estadosPermitidos = ['Registrado', 'En Proceso', 'Finalizado', 'Cancelado'];

    // Array Asociativo -> Diccionario
    $datosFormulario = [
        'nombre' => '',
        'apellidos' => '',
        'estado' => 'Registrado',
        'fecha' => ''
    ];

	// Contador de Estados
	$contadorEstados = [
		'Registrado' => 0, 
		'En Proceso' => 0, 
		'Finalizado' => 0, 
		'Cancelado' => 0
	];

    // Flujo GET: entrar en modo edición
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if($accionGet === 'editar' && $idGet !== null && isset($_SESSION['registros'][$idGet])) {
			$modoCookie = 'editar';
			setcookie('modo_formulario', 'editar', time() + 3600, '/');

			$datosFormulario['nombre'] = $_SESSION['registros'][$idGet]['nombre'];
			$datosFormulario['apellidos'] = $_SESSION['registros'][$idGet]['apellidos'];
			$datosFormulario['estado'] = $_SESSION['registros'][$idGet]['estado'];
		} else {
			$modoCookie = 'insertar';
			setcookie('modo_formulario', 'insertar', time() + 3600, '/');
		}
    }

    // Flujo POST: procesar formaulario edición/inserta
    if($_SERVER['REQUEST_METHOD'] === 'POST') {		
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $estado = $_POST['estado'] ?? 'Registrado';
        $modoPost = $_POST['modo'] ?? 'insertar';
        $idPost = isset($_POST['id']) ? (int) $_POST['id'] : null;

		// Validaciones

        $datosFormulario['nombre'] = $nombre;
        $datosFormulario['apellidos'] = $apellidos;
        $datosFormulario['estado'] = $estado;
        $datosFormulario['fecha'] = date('Y-m-d H:i:s');
        //$datosFormulario['fecha'] = new DateTime();

        // ERROR
        // Remplaza el valor de $_SESSION['registros'] por el contenido de la variable $datosFormulario
		// No inserta un elemento en la coleccion $_SESSION['registros']
        // $_SESSION['registros'] = $datosFormulario;

		if($modoCookie === 'insertar') {
			// Insertar el contenido de la variable $datosFormulario en el Array $_SESSION['registros']
			$_SESSION['registros'][] = $datosFormulario;

			// Equivalente:
			//array_push($_SESSION['registros'], $datosFormulario);

			// Equivalente:
			// $_SESSION['registros'][] = [
			//     'nombre' => $nombre,
			//     'apellidos' => $apellidos,
			//     'estado' => $estado,
			//     'fecha' => date('Y-m-d H:i:s')
			// ];

			// Equivalente:
			// array_push($_SESSION['registros'], [
			//     'nombre' => $nombre,
			//     'apellidos' => $apellidos,
			//     'estado' => $estado,
			//     'fecha' => date('Y-m-d H:i:s')
			// ]);

			$mensaje = 'Registro insertado correctamente.';
		} else {
			$_SESSION['registros'][$idGet]['nombre'] = $datosFormulario['nombre'];
			$_SESSION['registros'][$idGet]['apellidos'] = $datosFormulario['apellidos'];
			$_SESSION['registros'][$idGet]['estado'] = $datosFormulario['estado'];	
			
			$mensaje = 'Registro modificado correctamente.';
		}

		$datosFormulario['nombre'] = '';
		$datosFormulario['apellidos'] = '';
		$datosFormulario['estado'] = 'Registrado';		

		$modoCookie = 'insertar';
		setcookie('modo_formulario', 'insertar', time() + 3600, '/');
    }

	$resumenEstados = array_map(static function($item) {
		return $item['estado'];
	}, $_SESSION['registros']);		

	foreach($resumenEstados as $estadoResumen) {
		$contadorEstados[$estadoResumen]++;
	}

?>

<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>PHP + GLPI 11 | Formulario (Plantilla)</title>
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
					<h1 class="display-6 fw-bold mb-3">PHP Formulario</h1>
					<hr class="border-secondary-subtle opacity-100">
				</header>

				<section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
					<div class="text-center mx-auto" style="max-width: 960px;">
						<h2 class="h4 mb-4">Formulario con
							<span class="text-primary">$_GET</span>,
							<span class="text-success">$_POST</span>,
							<span class="text-warning">$_SESSION</span> y
							<span class="text-danger">$_COOKIE</span>
						</h2>

                        <!-- Opcion 1 -->
                        <?php 
                            // if(!empty($mensaje)) {
                            //     echo '<div class="alert alert-success text-start" role="alert">';
                            //     echo htmlspecialchars($mensaje) . '</div>';
                            // } 
                        ?>

                        <!-- Opcion 2 -->
                        <?php if(!empty($mensaje)): ?>
                            <div class="alert alert-success text-start" role="alert">
                                <?= htmlspecialchars($mensaje) ?>
                            </div>
                        <?php endif; ?>

						<div class="alert alert-secondary text-start" role="alert">
							<strong>Modo actual:</strong>
                            <?php 
                                switch($modoCookie) {
                                    case 'editar':
                                        echo 'Editar';
                                        break;
                                    case 'insertar':
                                        echo 'Insertar';
                                        break;
                                    default:
                                        echo 'Insertar';
                                        break;
                                }
                            ?>
						</div>

						<form method="post" action="" class="text-start border rounded p-4 bg-light-subtle mb-4">
							<input type="hidden" name="modo" value="<?= htmlspecialchars($modoCookie) ?>">
							<input type="hidden" name="id" value="<?= $idGet !== null ? $idGet : '' ?>">

							<div class="mb-3">
								<label for="nombre" class="form-label">Nombre</label>
								<input
									type="text"
									class="form-control"
									id="nombre"
									name="nombre"
									value="<?= htmlspecialchars($datosFormulario['nombre']) ?>"
									placeholder="Introduce el nombre"
								>
							</div>

							<div class="mb-3">
								<label for="apellidos" class="form-label">Apellidos</label>
								<input
									type="text"
									class="form-control"
									id="apellidos"
									name="apellidos"
									value="<?php echo htmlspecialchars($datosFormulario['apellidos']); ?>"
									placeholder="Introduce los apellidos"
								>
							</div>

							<div class="mb-3">
								<label for="estado" class="form-label">Estado</label>
								<select class="form-select" id="estado" name="estado">
                                    <?php 
                                        foreach($estadosPermitidos as $estado) {
                                            echo '<option value="' . htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') . '"' . ($datosFormulario['estado'] === $estado ? ' selected' : '')  .'>' . htmlspecialchars($estado) . '</option>';
                                        }
                                    ?>
								</select>
                                <br />
                                <select class="form-select" id="estado2" name="estado2">
                                    <?php foreach($estadosPermitidos as $estado): ?>                                    
                                    <option value="<?= htmlspecialchars($estado) ?>"<?= $datosFormulario['estado'] === $estado ? ' selected' : '' ?>>
                                        <?= htmlspecialchars($estado) ?>
                                    </option>
                                    <?php endforeach; ?>
								</select>
							</div>

							<div class="d-flex gap-2">
								<button type="submit" class="btn btn-primary"><?= $modoCookie === 'insertar' ? 'Insertar registro' : 'Guardar cambios' ?></button>
								<a href="?accion=nuevo" class="btn btn-outline-secondary">Limpiar / Nuevo</a>
							</div>
						</form>

						<div class="table-responsive text-start">
							<table class="table table-bordered table-striped align-middle">
								<thead class="table-dark">
									<tr>
										<th>#</th>
										<th>Nombre completo</th>
										<th>Estado</th>
										<th>Fecha de proceso</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>                                    
                                    <?php if(count($_SESSION['registros']) > 0): ?>
                                        <?php foreach($_SESSION['registros'] as $indice => $registro): ?>
                                            <tr>
                                                <td><?= ($indice + 1) ?></td>
                                                <td>
                                                    <?= htmlspecialchars($registro['nombre']) ?> <?= htmlspecialchars($registro['apellidos']) ?>
                                                </td>
                                                <td>
													<?php 
														$badge = 'secondary';
														switch($registro['estado']) {
															case 'Registrado':
																$badge = 'info';
																break;
															case 'En Proceso':
																$badge = 'warning';
																break;
															case 'Finalizado':
																$badge =  'success';
																break;	
															case 'Cancelado':
																$badge =  'danger';
																break;																																
														}

													?>
													<span class="badge text-bg-<?= $badge ?>">
														<?= htmlspecialchars($registro['estado']) ?>
													</span>
												</td>
                                                <td><?= htmlspecialchars($registro['fecha']) ?></td>
                                                <td><a href="?accion=editar&id=<?= $indice ?>" class="btn btn-sm btn-outline-primary">Editar</a></td>
                                            </tr>   
                                        <?php endforeach; ?>
                                    <?php endif; ?>
								</tbody>
							</table>
						</div>

						<div class="mt-4 text-start">
							<h3 class="h5">Resumen por estado (array_map + foreach)</h3>
							<ul>
								<li>Registrado: <strong><?= $contadorEstados['Registrado']; ?></strong></li>
								<li>En Proceso: <strong><?= $contadorEstados['En Proceso']; ?></strong></li>
								<li>Finalizado: <strong><?= $contadorEstados['Finalizado']; ?></strong></li>
								<li>Finalizado: <strong><?= $contadorEstados['Cancelado']; ?></strong></li>
							</ul>
						</div>

						<hr class="my-4">

						<div class="text-start">
							<h3 class="h5">Informacion de $_SERVER</h3>
							<div class="table-responsive">
								<table class="table table-sm table-hover">
									<thead>
										<tr>
											<th>Clave</th>
											<th>Valor</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($_SERVER as $clave => $valor): ?>
											<?php 
												if(is_array($valor)) {
													$valor = implode(', ', $valor);
												}
											?>
										<tr>
											<td><?= htmlspecialchars($clave) ?></td>
											<td><?= htmlspecialchars($valor) ?></td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</section>
			</div>
		</main>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
