@php
$driver = $getRecord();
@endphp

<div
class="
relative
rounded-xl border
p-4
text-center
transition
hover:shadow-lg hover:-translate-y-0.5
bg-white dark:bg-gray-900

{{ $driver->status === 'available'
? 'border-green-500'
: 'border-yellow-400'
}}
">

<!-- STATUS DOT -->

<div class="absolute top-2 right-2">
<span class="w-2 h-2 rounded-full block
{{ $driver->status === 'available'
? 'bg-green-500'
: 'bg-yellow-400'
}}"></span>
</div>


<!-- AVATAR -->

<div class="flex justify-center mb-3">

<img
src="{{ asset('storage/'.$driver->image) }}"
class="
w-16 h-16
rounded-full
object-cover
border-2

{{ $driver->status === 'available'
? 'border-green-500'
: 'border-yellow-400'
}}
">

</div>


<!-- NAME -->

<h3 class="text-sm font-semibold">
{{ $driver->name }}
</h3>


<!-- PHONE -->

<p class="text-xs text-gray-500">
{{ $driver->phone }}
</p>


<!-- ACTION -->

<a
href="{{ route('filament.admin.resources.drivers.edit', $driver->id) }}"
class="
inline-block mt-3
text-xs px-3 py-1
rounded-md
bg-primary-600
hover:bg-primary-500
text-white
transition
"
>
Edit
</a>

</div>