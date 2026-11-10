<?php
$pagina = 'produtos';
$titulo = 'Produtos';
require_once __DIR__ . '/../includes/header.php';

$produtos = db()->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Produtos</h1>
    <a href="<?= url('produtos/form.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo produto</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Nome</th><th>Descricao</th><th class="text-end">Peso (kg)</th><th class="text-end">Valor</th><th class="text-end">Acoes</th></tr>
            </thead>
            <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?= (int) $p['id'] ?></td>
                    <td><?= e($p['nome']) ?></td>
                    <td><?= e($p['descricao']) ?></td>
                    <td class="text-end"><?= number_format((float) $p['peso_kg'], 3, ',', '.') ?></td>
                    <td class="text-end"><?= moeda($p['valor']) ?></td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="<?= url('produtos/form.php?id=' . (int) $p['id']) ?>"><i class="bi bi-pencil"></i> Editar</a>
                        <a class="btn btn-sm btn-outline-danger" href="<?= url('produtos/excluir.php?id=' . (int) $p['id']) ?>"
                           onclick="return confirm('Excluir este produto?');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$produtos): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Nenhum produto cadastrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
