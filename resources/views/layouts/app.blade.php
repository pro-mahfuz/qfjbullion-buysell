@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle')
        | @yield('subtitle')
    @endif
@stop

{{-- Extend and customize the page content header --}}

@section('content_header')

    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        <a href="{{ config('app.company_url', '#') }}">
            {{ config('app.company_name', 'Shadhin GoldCRM') }}
        </a>
    </strong>
@stop

{{-- Add common Javascript/Jquery code --}}

@push('js')
    <script>
        $(document).ready(function() {
            @if (session()->has('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session()->has('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session()->has('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if (session()->has('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif

            @if (session()->has('errors'))
                @foreach (session('errors')->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif

            $(".common_select2").select2({
                // theme: 'classic',
                placeholder: "Select",
                allowClear: true,
            });

        });

        $(document).on('click', '.load_modal', function(e) {
            e.preventDefault();
            const url = $(this).attr("data-action");

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#dynamicModal').remove();
                    $('body').append(response);
                    $('#dynamicModal').modal('show');
                },
                error: function(xhr) {
                    console.error("Error loading modal:", xhr.statusText);
                }
            });
        });
        // $(document).on('click', '.load_modal', function(e) {
        //     e.preventDefault();
        //     const url = $(this).attr("data-action");

        //     // Prevent loading if a modal is already open
        //     if ($('.modal.show').length > 0) {
        //         console.warn("A modal is already open. Please close it first.");
        //         return;
        //     }

        //     $.ajax({
        //         url: url,
        //         type: 'GET',
        //         success: function(response) {
        //             if ($(response).find('.modal-content').length === 0) {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'Invalid Response',
        //                     text: 'Failed to load the modal content. Please try again.',
        //                 });
        //                 return;
        //             }

        //             // Clean up old modals and load new content
        //             $('#dynamicModal').remove();
        //             $('body').append(response);
        //             $('#dynamicModal').modal('show');
        //         },
        //         error: function(xhr) {
        //             console.error("Error loading modal:", xhr.statusText);
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Oops...',
        //                 text: 'Failed to load the modal. Please try again later.',
        //             });
        //         }
        //     });
        // });
    </script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="{{ asset('vendor/sweetalert2/sweetalert2.all.js') }}"></script>
@endpush

{{-- Add common CSS customizations --}}

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style type="text/css">
        {{-- You can add AdminLTE customizations here --}} .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 10px 16px;
            font-size: 18px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 85% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #CCC !important;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset;
            transition: border-color 0.15s ease-in-out 0s, box-shadow 0.15s ease-in-out 0s;
        }

        /* .swal2-container {
                        z-index: 9999 !important;
                    } */
        .swal-custom-toast {
            background-color: black !important;
            color: white !important;
            /* Ensure the text is visible on the black background */
        }
    </style>
@endpush
