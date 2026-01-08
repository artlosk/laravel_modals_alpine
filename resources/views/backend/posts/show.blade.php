@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-eye mr-2"></i>
            Просмотр поста: {{ $post->title }}
        </h1>
        <div class="d-flex">
            @can('edit-posts')
                <a href="{{ route('backend.posts.edit', $post) }}" class="btn btn-outline-warning btn-sm me-2">
                    <i class="fas fa-edit"></i> Редактировать
                </a>
            @endcan
            <a href="{{ route('backend.posts.index') }}" class="btn btn-outline-secondary btn-sm">
                Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-newspaper mr-2"></i>
                {{ $post->title }}
            </h3>
        </div>
        <div class="card-body">
            @include('backend.posts._show')

        </div>
    </div>
@endsection