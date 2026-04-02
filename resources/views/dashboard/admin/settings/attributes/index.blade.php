@extends('dashboard.admin.settings.layout')

@section('settings-content')

<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">

        <div>
            <h2 class="text-2xl font-semibold text-gray-900">
                Attributes
            </h2>

            <p class="text-sm text-gray-500">
                Manage product attributes used in filters and specifications
            </p>
        </div>


        <div class="flex items-center gap-3">

        

            <a href="{{ route('admin.settings.attributes.create') }}"
               class="px-4 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">

                + Add attribute

            </a>

        </div>

    </div>


    <div class="flex flex-col gap-6 max-w-4xl">


    {{-- Success message --}}
    @if(session('success'))

        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">

            {{ session('success') }}

        </div>

    @endif



    {{-- Attributes Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b">

                <tr>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Name
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Code
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Type
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Filterable
                    </th>

                    <th class="px-5 py-3 text-left font-medium text-gray-600">
                        Required
                    </th>

                    <th class="px-5 py-3 text-right font-medium text-gray-600">
                        Action
                    </th>

                </tr>

            </thead>



            <tbody class="divide-y">


                @foreach($attributes as $attribute)

                    <tr class="hover:bg-gray-50 transition">


                        {{-- Name --}}
                        <td class="px-5 py-3 font-semibold text-gray-900">

                            {{ $attribute->name }}

                        </td>



                        {{-- Code --}}
                        <td class="px-5 py-3 text-gray-700">

                            {{ $attribute->code }}

                        </td>



                        {{-- Type --}}
                        <td class="px-5 py-3 text-gray-700">

                            {{ ucfirst($attribute->type) }}

                        </td>



                        {{-- Filterable --}}
                        <td class="px-5 py-3 text-gray-700">

                            {{ $attribute->is_filterable ? 'Yes' : 'No' }}

                        </td>



                        {{-- Required --}}
                        <td class="px-5 py-3 text-gray-700">

                            {{ $attribute->is_required ? 'Yes' : 'No' }}

                        </td>



                        {{-- Actions --}}
                        <td class="px-5 py-3 text-right whitespace-nowrap">


                            {{-- Options --}}
                            @if(in_array($attribute->type,['select','multiselect']))

                                <a href="{{ route(
                                    'admin.settings.attributes.options.index',
                                    $attribute->id
                                ) }}"
                                   class="text-sm text-gray-700 hover:underline mr-3">

                                    Options

                                </a>

                            @endif



                            {{-- Edit --}}
                            <a href="{{ route(
                                'admin.settings.attributes.edit',
                                $attribute->id
                            ) }}"
                               class="text-sm text-gray-700 hover:underline mr-3">

                                Edit

                            </a>



                            {{-- Delete --}}
                            <form action="{{ route(
                                'admin.settings.attributes.destroy',
                                $attribute->id
                            ) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Delete attribute?')">

                                @csrf
                                @method('DELETE')


                                <button class="text-sm text-red-600 hover:underline">

                                    Delete

                                </button>

                            </form>


                        </td>


                    </tr>

                @endforeach


            </tbody>

        </table>

    </div>



    {{-- Pagination --}}
    <div class="px-5 py-4 border bg-gray-50">

        {{ $attributes->links() }}

    </div>


</div>

@endsection