<?php
require_once __DIR__ . '/../includes/helpers.php';

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);

$st = $pdo->prepare('SELECT f.*, t.nome AS t_nome, t.cpf_cnpj, t.telefone, t.veiculo, t.placa FROM fretes f
                     JOIN transportadores t ON t.id = f.transportador_id WHERE f.id = ?');
$st->execute([$id]);
$f = $st->fetch() ?: redirecionar('fretes/index.php', 'Frete nao encontrado.', 'danger');

$st = $pdo->prepare('SELECT * FROM enderecos WHERE id = ?');
$st->execute([$f['origem_id']]);
$origem = $st->fetch();
$st->execute([$f['destino_id']]);
$destino = $st->fetch();

$st = $pdo->prepare('SELECT p.nome, p.peso_kg, p.valor, fp.quantidade FROM frete_produtos fp
                     JOIN produtos p ON p.id = fp.produto_id WHERE fp.frete_id = ? ORDER BY p.nome');
$st->execute([$id]);
$itens = $st->fetchAll();

$pesoTotal = 0.0;
$valorMercadoria = 0.0;
foreach ($itens as $i) {
    $pesoTotal += $i['peso_kg'] * $i['quantidade'];
    $valorMercadoria += $i['valor'] * $i['quantidade'];
}

$pagina = 'fretes';
$titulo = 'Frete #' . $id;
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Frete #<?= $id ?> <small class="text-muted fs-6"><?= e(dataBr($f['data_frete'])) ?></small></h1>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="<?= url('fretes/form.php?id=' . $id) ?>"><i class="bi bi-pencil"></i> Editar</a>
        <a class="btn btn-outline-primary" href="<?= url('fretes/index.php') ?>">Voltar</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card h-100"><div class="card-body">
            <h2 class="h6 text-muted text-uppercase">Transportador</h2>
            <p class="mb-1 fw-semibold"><?= e($f['t_nome']) ?></p>
            <p class="mb-0 small text-muted">
                <?= e($f['cpf_cnpj']) ?><br>
                <?= e($f['telefone']) ?><br>
                <?= e(trim($f['veiculo'] . ' ' . $f['placa'])) ?>
            </p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card h-100"><div class="card-body">
            <h2 class="h6 text-muted text-uppercase">Origem</h2>
            <p class="mb-1 fw-semibold"><?= e($origem['apelido']) ?></p>
            <p class="mb-0 small text-muted"><?= e(enderecoTexto($origem)) ?></p>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card h-100"><div class="card-body">
            <h2 class="h6 text-muted text-uppercase">Destino</h2>
            <p class="mb-1 fw-semibold"><?= e($destino['apelido']) ?></p>
            <p class="mb-0 small text-muted"><?= e(enderecoTexto($destino)) ?></p>
        </div></div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header bg-white fw-semibold">Produtos transportados</div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light"><tr><th>Produto</th><th class="text-end">Qtd.</th><th class="text-end">Peso unit. (kg)</th><th class="text-end">Valor unit.</th></tr></thead>
            <tbody>
            <?php foreach ($itens as $i): ?>
                <tr>
                    <td><?= e($i['nome']) ?></td>
                    <td class="text-end"><?= (int) $i['quantidade'] ?></td>
                    <td class="text-end"><?= number_format((float) $i['peso_kg'], 3, ',', '.') ?></td>
                    <td class="text-end"><?= moeda($i['valor']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4"><div class="card card-stat"><div class="card-body">
        <div class="text-muted small">Peso total</div><div class="h4 mb-0"><?= number_format($pesoTotal, 3, ',', '.') ?> kg</div>
    </div></div></div>
    <div class="col-md-4"><div class="card card-stat"><div class="card-body">
        <div class="text-muted small">Valor da mercadoria</div><div class="h4 mb-0"><?= moeda($valorMercadoria) ?></div>
    </div></div></div>
    <div class="col-md-4"><div class="card card-stat"><div class="card-body">
        <div class="text-muted small">Valor do frete</div><div class="h4 mb-0 text-success"><?= moeda($f['valor_frete']) ?></div>
    </div></div></div>
</div>
<?php if ($f['observacao']): ?>
    <p class="mt-3 text-muted"><strong>Observacao:</strong> <?= e($f['observacao']) ?></p>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
