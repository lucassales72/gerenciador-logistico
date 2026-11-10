<?php
require_once __DIR__ . '/helpers.php';
$pagina = $pagina ?? '';
$titulo = $titulo ?? 'Gerenciador Logistico';
$menu = [
    'inicio'          => ['Inicio', ''],
    'produtos'        => ['Produtos', 'produtos/index.php'],
    'enderecos'       => ['Enderecos', 'enderecos/index.php'],
    'transportadores' => ['Transportadores', 'transportadores/index.php'],
    'fretes'          => ['Fretes', 'fretes/index.php'],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | Gerenciador Logistico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="<?= url() ?>"><i class="bi bi-truck"></i> Gerenciador Logistico</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Alternar navegacao">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto">
                <?php foreach ($menu as $chave => [$rotulo, $link]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $pagina === $chave ? 'active' : '' ?>"
                           href="<?= url($link) ?>"><?= e($rotulo) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container pb-5">
<?php exibirFlash(); ?>
