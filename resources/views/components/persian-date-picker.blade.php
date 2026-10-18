@props([
    'name' => 'date',
    'value' => null,
    'label' => null,
    'placeholder' => 'انتخاب تاریخ شمسی...',
    'required' => false,
    'minYear' => 1320,
    'maxYear' => 1410,
])

<x-persian-datetime-picker
    :name="$name"
    :value="$value"
    :label="$label"
    :placeholder="$placeholder"
    :required="$required"
    :minYear="$minYear"
    :maxYear="$maxYear"
    :showTime="false"
    {{ $attributes }}
/>
