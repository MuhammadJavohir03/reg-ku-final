<x-layouts.sidebar :title="__('O\'quv yili')">

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('O\'quv yili') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @include('oquv_yili.index')
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(event) {
            event.preventDefault();
            if (confirm('Haqiqatan ham o\'chirmoqchimisiz?')) {
                event.target.submit();
            }
        }
    </script>
</x-layouts.sidebar>
