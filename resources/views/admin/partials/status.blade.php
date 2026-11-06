@php
    $map = ['Paid' => 'ok', 'Published' => 'ok', 'Active' => 'ok', 'Pending' => 'wait', 'Draft' => 'wait', 'Refunded' => 'bad', 'Blocked' => 'bad'];
@endphp
<span class="admin-status admin-status-{{ $map[$status] ?? 'wait' }}">{{ $status }}</span>
