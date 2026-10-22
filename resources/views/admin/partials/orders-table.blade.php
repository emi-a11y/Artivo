<div class="table-responsive">
    <table class="table admin-table align-middle mb-0">
        <thead>
            <tr><th scope="col">Order</th><th scope="col">Customer</th><th scope="col">Items</th><th scope="col">Total</th><th scope="col">Date</th><th scope="col">Status</th></tr>
        </thead>
        <tbody>
            @forelse ($orders as $o)
                <tr>
                    <td>{{ $o['id'] }}</td>
                    <td>{{ $o['customer'] }}</td>
                    <td>{{ $o['items'] }}</td>
                    <td>${{ number_format($o['total'], 2) }}</td>
                    <td>{{ $o['date'] }}</td>
                    <td>@include('admin.partials.status', ['status' => $o['status']])</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No orders in this tab.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
