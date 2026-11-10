<?php
$pagina = 'fretes';
$titulo = 'Fretes';
require_once __DIR__ . '/../includes/header.php';

$filtro = $_GET['data'] ?? '';
$filtro = dataValida($filtro) ? $filtro : '';

$sql = 'SELECT f.*, t.nome AS transportador, o.apelido AS origem, d.apelido AS destino,
               (SELECT COUNT(*) FROM frete_produtos fp WHERE fp.frete_id = f.id) AS qtd_itens
        FROM fretes f
        JOIN transportadores t ON t.id = f.transportador_id
        JOIN enderecos o ON o.id = f.origem_id
        JOIN enderecos d ON d.id = f.destino_id';
$params = [];
if ($filtro !== '') {
    $sql .= ' WHERE f.data_frete = ?';
    $params[] = $filtro;
}
$sql .= ' ORDER BY f.data_frete DESC, f.id DESC';
$st = db()->prepare($sql);
$st->execute($params);
$fretes = $st->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Fretes</h1>
    <a href="<?= url('fretes/form.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Gerar frete</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="get" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="data" class="form-label">Data do frete</label>
                <input type="date" class="form-control" id="data" name="data" value="<?= e($filtro) ?>">
            </div>
            <div class="col-md-8 d-flex flex-wrap gap-2">
                <button class="btn btn-outline-primary" type="submit"><i class="bi bi-funnel"></i> Filtrar</button>
                <a class="btn btn-outline-secondary" href="<?= url('fretes/index.php') ?>">Limpar</a>
                <button class="btn btn-danger ms-md-auto" type="submit" formaction="<?= url('fretes/relatorio.php') ?>"
                        title="Baixa o PDF com os fretes da data escolhida"><i class="bi bi-file-earmark-pdf"></i> Baixar relatorio do dia (PDF)</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>No</th><th>Data</th><th>Transportador</th><th>Origem</th><th>Destino</th><th class="text-center">Itens</th><th class="text-end">Valor</th><th class="text-end">Acoes</th></tr>
            </thead>
            <tbody>
            <?php foreach ($fretes as $f): ?>
                <tr>
                    <td><?= (int) $f['id'] ?></td>
                    <td><?= e(dataBr($f['data_frete'])) ?></td>
                    <td><?= e($f['transportador']) ?></td>
                    <td><?= e($f['origem']) ?></td>
                    <td><?= e($f['destino']) ?></td>
                    <td class="text-center"><span class="badge text-bg-secondary"><?= (int) $f['qtd_itens'] ?></span></td>
                    <td class="text-end"><?= moeda($f['valor_frete']) ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-primary" href="<?= url('fretes/ver.php?id=' . (int) $f['id']) ?>"><i class="bi bi-eye"></i> Ver</a>
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('fretes/form.php?id=' . (int) $f['id']) ?>"><i class="bi bi-pencil"></i></a>
                        <a class="btn btn-sm btn-outline-danger" href="<?= url('fretes/excluir.php?id=' . (int) $f['id']) ?>"
                           onclick="return confirm('Excluir este frete?');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$fretes): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Nenhum frete encontrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
