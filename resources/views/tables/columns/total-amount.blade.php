<div>
    @php
        $record = $getRecord();
        if ($record->paymentabel_type && $record->paymentabel_id) {
            $payment = $record->paymentabel_type::find($record->paymentabel_id);
            echo $payment->total_amount;
        } else {
            echo '-';
        }
    @endphp
</div>
