<table class="table">
    <thead>
        <tr>
            <th>Task</th>
            <th>Spalte</th>
            <th>Person</th>
            <th>Notizen</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tasks as $task): ?>
        <tr>
            <td><?= esc($task['tasks']) ?></td>
            <td><?= esc($task['spalte']) ?></td>
            <td><?= esc($task['vorname']) ?> <?= esc($task['name']) ?></td>
            <td><?= esc($task['notizen']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
