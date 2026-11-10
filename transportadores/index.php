<?php
$pagina = 'transportadores';
$titulo = 'Transportadores';
require_once __DIR__ . '/../includes/header.php';

$transportadores = db()->query('SELECT * FROM transportadores ORDER BY nome')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Transportadores</h1>
    <a href="<?= url('transportadores/form.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo transportador</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Nome</th><th>CPF/CNPJ</th><th>Telefone</th><th>Veiculo</th><th>Placa</th><th class="text-end">Acoes</th></tr>
            </thead>
            <tbody>
            <?php foreach ($transportadores as $t): ?>
                <tr>
                    <td><?= (int) $t['id'] ?></td>
                    <td><?= e($t['nome']) ?></td>
                    <td><?= e($t['cpf_cnpj']) ?></td>
                    <td><?= e($t['telefone']) ?></td>
                    <td><?= e($t['veiculo']) ?></td>
                    <td><?= e($t['placa']) ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('transportadores/form.php?id=' . (int) $t['id']) ?>"><i class="bi bi-pencil"></i> Editar</a>
                        <a class="btn btn-sm btn-outline-danger" href="<?= url('transportadores/excluir.php?id=' . (int) $t['id']) ?>"
                           onclick="return confirm('Excluir este transportador?');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$transportadores): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Nenhum transportador cadastrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
