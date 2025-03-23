<div>
    @php
        $record = $getRecord();
        if ($record->oderabel_type && $record->oderabel_id) {
            $orderStatus = $record->oderabel_type::find($record->oderabel_id)?->status;
            if ($orderStatus == '4'){
                echo "Waiting";
            } elseif ($orderStatus == '5'){
                echo "Processing";
            } elseif ($orderStatus == '6'){
                echo "Delivered";
            } else{
                echo "Cancelled";
            }
        } else {
            echo '-';
        }
    @endphp
</div>
