<div>
    @php
        $record = $getRecord();
        if ($record->paymentabel_type && $record->paymentabel_id) {
            $paymentStatus = $record->paymentabel_type::find($record->paymentabel_id)?->payment_status;
            if ($paymentStatus == '1'){
                echo "Completed";
            } elseif ($paymentStatus == '2'){
                echo "Pending";
            } elseif ($paymentStatus == '3'){
                echo "Return";
            } else{
                echo "Cancelled";
            }
        } else {
            echo '-';
        }
    @endphp
</div>
