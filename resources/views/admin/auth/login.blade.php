<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Đăng nhập quản trị</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Vui lòng đăng nhập bằng tài khoản có quyền quản trị.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/20 dark:text-red-200">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
        </div>

        <div>
            <x-input-label for="password" :value="__('Mật khẩu')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                <input id="remember" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ml-2">Ghi nhớ đăng nhập</span>
            </label>

            <a href="{{ url('/') }}" class="text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                Quay lại trang chủ
            </a>
        </div>

        <div>
            <x-primary-button class="w-full justify-center">
                Đăng nhập
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

