<div class="table-responsive">
    <table class="table admin-table align-middle mb-0">
        <thead>
            <tr>
                <th scope="col">Order</th><th scope="col">Customer</th><th scope="col">Items</th><th scope="col">Total</th><th scope="col">Date</th><th scope="col">Status</th>
                @if ($editable ?? false)<th scope="col" class="text-end">Change status</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $o)
                <tr>
                    <td>{{ $o->code }}</td>
                    <td>{{ $o->user->name }}</td>
                    <td>{{ $o->items_count }}</td>
                    <td>${{ number_format($o->total, 2) }}</td>
                    <td class="text-nowrap">{{ $o->created_at->format('M j, Y') }}</td>
                    <td>@include('admin.partials.status', ['status' => $o->status])</td>
                    @if ($editable ?? false)
                        <td class="text-end">
                            <form action="{{ route('admin.orders.update', $o->id) }}" method="post" class="admin-inline-form">
                                @csrf
                                @method('PATCH')
                                <label for="status{{ $o->id }}" class="visually-hidden">Status for {{ $o->code }}</label>
                                <select id="status{{ $o->id }}" name="status" class="form-select form-select-sm">
                                    @foreach (\App\Models\Order::STATUSES as $s)<option @selected($o->status === $s)>{{ $s }}</option>@endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-dark">Save</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ ($editable ?? false) ? 7 : 6 }}" class="text-center text-muted py-4">No orders here yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
