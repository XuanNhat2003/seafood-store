@php
    $badgeMap = [
        'pending'   => 'badge-status-unpaid',
        'confirmed' => 'badge-status-processing',
        'shipping'  => 'badge-status-shipped',
        'delivered' => 'badge-status-paid',
        'cancelled' => 'badge-status-cancelled',
    ];
@endphp
<span class="badge-status {{ $badgeMap[strtolower((string) $status)] ?? 'bg-secondary text-white' }}">{{ status_label($status) }}</span>
