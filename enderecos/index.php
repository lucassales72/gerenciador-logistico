<?php
$pagina = 'enderecos';
$titulo = 'Enderecos';
require_once __DIR__ . '/../includes/header.php';

$enderecos = db()->query('SELECT * FROM enderecos ORDER BY apelido')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Enderecos <small class="text-muted fs-6">(origem e destino)</small></h1>
    <a href="<?= url('enderecos/form.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo endereco</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Apelido</th><th>Endereco</th><th>Cidade/UF</th><th class="text-end">Acoes</th></tr>
            </thead>
            <tbody>
            <?php foreach ($enderecos as $en): ?>
                <tr>
                    <td><?= (int) $en['id'] ?></td>
                    <td><?= e($en['apelido']) ?></td>
                    <td><?= e($en['logradouro'] . ', ' . $en['numero'] . ($en['complemento'] ? ' - ' . $en['complemento'] : '') . ' - ' . $en['bairro']) ?><br>
                        <small class="text-muted">CEP <?= e($en['cep']) ?></small></td>
                    <td><?= e($en['cidade'] . '/' . $en['uf']) ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('enderecos/form.php?id=' . (int) $en['id']) ?>"><i class="bi bi-pencil"></i> Editar</a>
                        <a class="btn btn-sm btn-outline-danger" href="<?= url('enderecos/excluir.php?id=' . (int) $en['id']) ?>"
                           onclick="return confirm('Excluir este endereco?');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$enderecos): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Nenhum endereco cadastrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
