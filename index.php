<?php
$pagina = 'inicio';
$titulo = 'Inicio';
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$total = fn(string $tabela): int => (int) $pdo->query("SELECT COUNT(*) FROM {$tabela}")->fetchColumn();
$cards = [
    ['Produtos', $total('produtos'), 'produtos/index.php', 'bi-box-seam', 'primary'],
    ['Enderecos', $total('enderecos'), 'enderecos/index.php', 'bi-geo-alt', 'info'],
    ['Transportadores', $total('transportadores'), 'transportadores/index.php', 'bi-person-badge', 'warning'],
    ['Fretes', $total('fretes'), 'fretes/index.php', 'bi-truck', 'success'],
];

$st = $pdo->prepare('SELECT COUNT(*) AS qtd, COALESCE(SUM(valor_frete), 0) AS soma FROM fretes WHERE data_frete = ?');
$st->execute([date('Y-m-d')]);
$hoje = $st->fetch();
?>
<div class="mb-4">
    <h1 class="h3">Painel</h1>
    <p class="text-muted mb-0">Cadastre produtos, enderecos e transportadores e depois gere os fretes.</p>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($cards as [$rotulo, $qtd, $link, $icone, $cor]): ?>
        <div class="col-6 col-lg-3">
            <a href="<?= url($link) ?>" class="text-decoration-none">
                <div class="card card-stat h-100"><div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small"><?= e($rotulo) ?></div>
                        <div class="display-6 text-dark"><?= $qtd ?></div>
                    </div>
                    <i class="bi <?= $icone ?> fs-1 text-<?= $cor ?>"></i>
                </div></div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5">Fretes de hoje (<?= date('d/m/Y') ?>)</h2>
            <p class="mb-1"><strong><?= (int) $hoje['qtd'] ?></strong> frete(s) &middot; <strong><?= moeda($hoje['soma']) ?></strong></p>
            <a class="btn btn-danger btn-sm mt-2" href="<?= url('fretes/relatorio.php?data=' . date('Y-m-d')) ?>"><i class="bi bi-file-earmark-pdf"></i> Baixar relatorio de hoje</a>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100"><div class="card-body">
            <h2 class="h5">Como usar</h2>
            <ol class="mb-0">
                <li>Cadastre os <a href="<?= url('produtos/index.php') ?>">produtos</a>.</li>
                <li>Cadastre os <a href="<?= url('enderecos/index.php') ?>">enderecos</a> de origem e destino.</li>
                <li>Cadastre o <a href="<?= url('transportadores/index.php') ?>">transportador</a>.</li>
                <li><a href="<?= url('fretes/form.php') ?>">Gere o frete</a> relacionando tudo.</li>
            </ol>
        </div></div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
