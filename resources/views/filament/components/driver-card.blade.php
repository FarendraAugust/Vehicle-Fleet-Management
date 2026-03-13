<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">

    <div
        class="
            p-8
            rounded-2xl
            border-2
            bg-white dark:bg-gray-900
            hover:shadow-xl
            hover:-translate-y-1
            transition
            min-h-[320px]
            flex flex-col
            items-center
            text-center
            gap-4
            {{ $getRecord()->status === 'available'
                ? 'border-green-500'
                : 'border-yellow-400'
            }}
        "
    >

        <img
            src="{{ asset('storage/'.$getRecord()->image) }}"
            class="w-28 h-28 rounded-full object-cover shadow"
        >

        <div class="font-bold text-lg">
            {{ $getRecord()->name }}
        </div>

        <div class="text-sm text-gray-500 flex items-center gap-1">
            <x-heroicon-m-phone class="w-4 h-4"/>
            {{ $getRecord()->phone }}
        </div>

        <div>
            @if($getRecord()->status === 'available')
                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                    Available
                </span>
            @else
                <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                    {{ ucfirst($getRecord()->status) }}
                </span>
            @endif
        </div>

    </div>

</div>