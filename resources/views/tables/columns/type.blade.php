<div>
    @php
        $record = $getRecord();
        if($record->last_created_date || $record->last_created_date == ''){
return new HtmlString('<span class="text-green-500">Recurring Order</span>');
} else {
return new HtmlString('<span class="text-blue-500">Order</span>');
}
    @endphp
</div>
