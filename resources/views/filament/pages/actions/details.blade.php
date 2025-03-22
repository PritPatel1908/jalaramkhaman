<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-black-500">
        <thead class="text-xs text-black uppercase bg-gray-800">
            <tr>
                <th scope="col" class="px-6 py-3">#</th>
                <th scope="col" class="px-6 py-3">Date</th>
                <th scope="col" class="px-6 py-3">Products</th>
                <th scope="col" class="px-6 py-3">Order Status</th>
                <th scope="col" class="px-6 py-3">Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @if ($detail->oderabel_type === 'App\Models\Order')
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        {{ $detail->id }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($detail->created_date)->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $orders = $detail->oderabel_type::find($detail->oderabel_id)?->order_details;
                            $productNames = $orders ? $orders->pluck('products.name')->toArray() : [];
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach ($productNames as $name)
                                <x-filament::badge color="info">
                                    {{ $name }}
                                </x-filament::badge>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $orderStatus = $detail->oderabel_type::find($detail->oderabel_id)?->status;
                        @endphp
                        <span
                            class="px-2 py-1 text-xs font-semibold rounded-full"
                        >
                            @if ($orderStatus == '4')
                                <x-filament::badge color="info">
                                    Waiting
                                </x-filament::badge>
                            @elseif ($orderStatus == '5')
                                <x-filament::badge color="warning">
                                    Processing
                                </x-filament::badge>
                            @elseif ($orderStatus == '6')
                                <x-filament::badge color="success">
                                    Delivered
                                </x-filament::badge>
                            @else
                                <x-filament::badge color="danger">
                                    Cancelled
                                </x-filament::badge>
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $paymentStatus = $detail->paymentabel_type::find($detail->paymentabel_id)?->payment_status;
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full">
                            @if ($paymentStatus == '1')
                            <x-filament::badge color="success">
                                Completed
                            </x-filament::badge>
                            @elseif ($paymentStatus == '2')
                            <x-filament::badge color="warning">
                                Pending
                            </x-filament::badge>
                            @elseif ($paymentStatus == '3')
                            <x-filament::badge color="info">
                                Return
                            </x-filament::badge>
                            @else
                            <x-filament::badge color="danger">
                                Cancelled
                            </x-filament::badge>
                            @endif
                        </span>
                    </td>
                </tr>
            @else
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        {{ $detail->id }}
                    </td>
                    <td class="px-6 py-4">
                        {{ \Carbon\Carbon::parse($detail->created_date)->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $orders = $detail->oderabel_type::find($detail->oderabel_id)?->recurring_order_detail_schedules;
                        $productNames = $orders ? $orders->pluck('products.name')->toArray() : [];
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @foreach ($productNames as $name)
                            <x-filament::badge color="info">
                                {{ $name }}
                            </x-filament::badge>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $orderStatus = $detail->oderabel_type::find($detail->oderabel_id)?->status;
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full">
                            @if ($orderStatus == '4')
                            <x-filament::badge color="info">
                                Waiting
                            </x-filament::badge>
                            @elseif ($orderStatus == '5')
                            <x-filament::badge color="warning">
                                Processing
                            </x-filament::badge>
                            @elseif ($orderStatus == '6')
                            <x-filament::badge color="success">
                                Delivered
                            </x-filament::badge>
                            @else
                            <x-filament::badge color="danger">
                                Cancelled
                            </x-filament::badge>
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $paymentStatus = $detail->paymentabel_type::find($detail->paymentabel_id)?->payment_status;
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full">
                            @if ($paymentStatus == '1')
                            <x-filament::badge color="success">
                                Completed
                            </x-filament::badge>
                            @elseif ($paymentStatus == '2')
                            <x-filament::badge color="warning">
                                Pending
                            </x-filament::badge>
                            @elseif ($paymentStatus == '3')
                            <x-filament::badge color="info">
                                Return
                            </x-filament::badge>
                            @else
                            <x-filament::badge color="danger">
                                Cancelled
                            </x-filament::badge>
                            @endif
                        </span>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
