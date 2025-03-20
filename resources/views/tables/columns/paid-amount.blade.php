<div>
    @php
        $record = $getRecord();
        if ($record->paymentabel_type && $record->paymentabel_id) {
            $payment = $record->paymentabel_type::find($record->paymentabel_id);
            echo $payment->complate_payment_amount ? $payment->complate_payment_amount :'0';
        } else {
            echo '-';
        }
    @endphp
</div>
