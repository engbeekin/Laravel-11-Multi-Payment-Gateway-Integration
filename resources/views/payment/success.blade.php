<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Success
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="alert">
                    <h2 class="alert alert-success text-white bg-success">Successfully payment!</h2>
                </div>
                @if(session('message'))
                    {{session('message')}}
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

