@extends('layouts.admin')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-user-plus mr-2"></i>
            Создание пользователя
        </h1>
        <div class="d-flex">
            <a href="{{ route('backend.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Назад к списку
            </a>
        </div>
    </div>
@endsection

@section('admin_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-plus mr-2"></i>
                Новая учетная запись
            </h3>
        </div>
        <div class="card-body">
            @include('backend.users._form')

        </div>
    </div>
@endsection