@props([
    'name',
])

<div {{ $attributes->merge([
    'class' => 'w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center'
]) }}>

    <x-dynamic-component
        :component="'icons.contact.' . $name"
        class="w-5 h-5 text-gray-500"
    />

</div>