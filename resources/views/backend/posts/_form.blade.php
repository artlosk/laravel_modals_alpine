<form id="postForm" action="{{ isset($post) ? route('backend.posts.update', $post) : route('backend.posts.store') }}"
    method="POST" enctype="multipart/form-data"
    onsubmit="event.preventDefault(); window.dispatchEvent(new CustomEvent('submit-modal-form', { detail: { form: this } }))">
    @csrf
    @if(isset($post))
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="title" class="form-label" form-label class="form-label">
            <i class="fas fa-heading mr-1"></i> Заголовок поста
        </label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $post->title ?? '') }}" placeholder="Введите заголовок поста" required>
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="content" class="form-label" form-label class="form-label">
            <i class="fas fa-align-left mr-1"></i> Содержимое поста
        </label>
        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="8"
            placeholder="Введите содержимое поста" required>{{ old('content', $post->content ?? '') }}</textarea>
        @error('content')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">
            <i class="fas fa-images mr-1"></i> Медиафайлы
        </label>
        <x-media-gallery name="media" :initialMedia="$post->relatedMedia ?? []" />
        @error('media.*')
            <div class="text-danger mt-2">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary me-2">
            <i class="fas fa-save"></i> {{ isset($post) ? 'Сохранить изменения' : 'Создать пост' }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.dispatchEvent(new CustomEvent('close-modal'))">
            Отмена
        </button>
    </div>
</form>