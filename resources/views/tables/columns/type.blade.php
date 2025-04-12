<div>
    @php
        $record = $getRecord();
        if($record->last_created_date || $record->last_created_date == ''){
            echo 'Recurring Order';
        } else {
            echo 'Order';
        }
    @endphp
</div>
