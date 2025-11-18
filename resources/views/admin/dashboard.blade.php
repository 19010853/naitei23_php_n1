<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Bảng điều khiển quản trị') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="border-b border-gray-200 bg-white px-4 py-5 dark:border-gray-700 dark:bg-gray-900 sm:px-6">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($stats as $stat)
                            <div class="overflow-hidden rounded-lg bg-indigo-50 px-4 py-5 shadow sm:p-6 dark:bg-indigo-900/30">
                                <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $stat['label'] }}
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                    {{ number_format($stat['value']) }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

