<div class="row" id="media-grid">
    @forelse($mediaItems as $item)
        <div class="col-md-2 col-sm-4 col-6 mb-4">
            <div class="card h-100 media-item {{ in_array($item->id, $selectedIds ?? []) ? 'selected' : '' }}"
                data-media-id="{{ $item->id }}">
                <div class="card-body p-2 d-flex align-items-center justify-content-center"
                    style="height: 120px; overflow: hidden; position: relative;">
                    @if(str_starts_with($item->mime_type, 'image/'))
                        <img src="{{ $item->getUrl('thumb') }}" class="img-fluid" alt="{{ $item->name }}" loading="lazy">
                    @else
                        <i class="fas fa-file fa-3x text-secondary"></i>
                    @endif
                    <div class="media-overlay">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="check-{{ $item->id }}">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 delete-media-btn"
                        data-media-id="{{ $item->id }}" title="Удалить">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-footer p-2 text-truncate text-center small" title="{{ $item->name }}">
                    {{ $item->name }}
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Медиафайлы не найдены</p>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $mediaItems->links() }}
</div>