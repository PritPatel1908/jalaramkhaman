<div>
    @php
        $record = $getRecord();
        if($record->last_created_date || $record->last_created_date == ''){
return 'Recurring Order';
} else {
return 'Order';
}
    @endphp
</div>
