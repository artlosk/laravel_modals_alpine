@extends('adminlte::page')

@section('title', 'Admin Panel')

@section('css')
@vite(['resources/css/backend/admin.css', 'resources/css/backend/admin-custom.css'])
<script>
    (function () {
        const savedDarkMode = localStorage.getItem('darkMode');
        if (savedDarkMode === 'true') {
            if (document.documentElement && document.documentElement.classList) {
                document.documentElement.classList.add('dark-mode');
            }
            if (document.body && document.body.classList) {
                document.body.classList.add('dark-mode');
            }
        }
    })();
</script>
@stop

@section('content_top_nav_right')
<li class="nav-item" style="margin-right: 8px;">
    <button class="btn btn-outline-primary btn-sm" onclick="toggleDarkMode()" id="dark-mode-toggle-btn"
        data-dark-mode-toggle="true" style="margin: 0; vertical-align: middle;">
        <i class="fas fa-moon"></i> <span class="d-none d-md-inline">Темная тема</span>
    </button>
</li>
@stop

@section('js')
<script>
    window.appConfig = {
        routes: {
            mediaIndex: "{{ route('backend.media.index') }}",
            mediaGetByIds: "{{ route('backend.media.getByIds') }}",
            filepondUpload: "{{ route('backend.filepond.upload') }}",
            filepondDelete: "{{ route('backend.filepond.delete') }}"
        }
    };
</script>
@vite(['resources/js/backend/admin.js', 'resources/js/backend/admin-media.js'])
@stop

@section('content_header')
<h1>Admin Dashboard</h1>
@stop

@section('content')
<div x-data="managerModals">
    @yield('admin_content')

    <!-- Modal Backdrop -->
    <div class="modal-backdrop fade show" x-show="showModal" style="display: none; z-index: 1040;"
        :style="{ display: showModal ? 'block' : 'none' }"></div>

    <!-- Modal -->
    <div class="modal fade" :class="{ 'show': showModal }" style="display: none; z-index: 1050;"
        :style="{ display: showModal ? 'block' : 'none' }" tabindex="-1" role="dialog" aria-modal="true"
        :aria-hidden="!showModal" @click.self="closeModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" x-text="modalTitle"></h5>
                    <button type="button" class="close" @click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body position-relative">
                    <!-- Preloader -->
                    <div x-show="isLoading" class="position-absolute top-50 start-50 translate-middle text-center"
                        style="z-index: 10;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <!-- Content -->
                    <div x-show="!isLoading" x-html="modalContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container for AJAX Errors -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1060">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast show align-items-center text-white border-0 mb-2"
                 :class="{'bg-success': toast.type === 'success', 'bg-danger': toast.type === 'error'}"
                 role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" x-text="toast.message"></div>
                    <button type="button" class="close text-white me-2 m-auto" @click="removeToast(toast.id)" aria-label="Close" style="background: none; border: none; font-size: 1.5rem; opacity: 0.8; line-height: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
@stop