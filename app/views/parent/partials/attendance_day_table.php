<?php if (!empty($records)): ?>
<table class="table table-striped table-bordered align-middle">
    <thead class="table-primary">
        <tr>
            <th>درس</th>
            <th>وضعیت</th>
            <th>معلم</th>
            <th>توضیحات</th>
            <th>ساعت ثبت</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $rec): ?>
            <tr>
                <td><?php echo htmlspecialchars($rec['lesson_name']); ?></td>
                <td>
                    <?php
                    $map = [
                        'present' => ['حاضر', 'success'],
                        'absent'  => ['غایب', 'danger'],
                        'late'    => ['تأخیر', 'warning'],
                        'excused' => ['عذر موجه', 'secondary']
                    ];
                    [$label, $cls] = $map[$rec['status']] ?? ['نامشخص', 'light'];
                    echo "<span class='badge bg-$cls'>$label</span>";
                    ?>
                </td>
                <td><?php echo htmlspecialchars($rec['teacher_name']); ?></td>
                <td><?php echo htmlspecialchars($rec['note']); ?></td>
                <td><?php echo date('H:i', strtotime($rec['record_time'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="text-center text-muted py-4">برای این روز حضور و غیابی ثبت نشده است.</div>
<?php endif; ?>
