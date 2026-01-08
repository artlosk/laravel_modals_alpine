@extends('layouts.admin')

@php
    use Illuminate\Support\Str;
@endphp

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-images mr-2"></i>
            Медиафайлы
        </h1>
        <div class="d-flex">
            @can('upload-media')
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload"></i> Загрузить файлы
                </button>
            @endcan
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>
                Библиотека медиафайлов
            </h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div id="media-gallery-container">
                @include('backend.media._content')
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    @can('upload-media')
        <div class="modal fade" id="uploadModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-upload mr-2"></i>
                            Загрузка файлов
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="mediaFiles" class="form-label" form-label class="form-label">Выберите
                                    файлы</label>
                                <input type="file" class="form-control" id="mediaFiles" name="files[]" multiple
                                    accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                            </div>
                            <div class="progress" style="display: none;">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" onclick="uploadFiles()">
                            <i class="fas fa-upload"></i> Загрузить
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function () {
                // Show success feedback
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-info');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-info');
                }, 2000);
            });
        }

        function uploadFiles() {
            const formData = new FormData(document.getElementById('uploadForm'));
            const progressBar = document.querySelector('.progress-bar');
            const progressContainer = document.querySelector('.progress');

            progressContainer.style.display = 'block';

            fetch('{{ route("backend.media.upload") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка загрузки: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка при загрузке файлов');
                });
        }

        // Delete media functionality
        document.addEventListener('click', function (e) {
            if (e.target.closest('.delete-media-btn')) {
                const mediaId = e.target.closest('.delete-media-btn').dataset.mediaId;
                if (confirm('Вы уверены, что хотите удалить этот файл?')) {
                    fetch(`{{ url('/admin/media') }}/${mediaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`[data-media-id="${mediaId}"]`).remove();
                            } else {
                                alert('Ошибка удаления: ' + data.message);
                            }
                        });
                }
            }
        });
    </script>
@endsection