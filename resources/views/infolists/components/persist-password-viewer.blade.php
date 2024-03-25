<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div x-data="{ password: localStorage.getItem('{{ $getState() }}') }">
        <span x-text="password"></span>
    </div>
</x-dynamic-component>
