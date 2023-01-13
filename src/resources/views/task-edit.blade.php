@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-white rounded border pt-3 pb-3">
            <div class="messageWrapper"></div>
            <form class="row g-3" name="taskForm" method="POST">
                <input type="hidden"  id="id"  name="id" value="{{ $task['id'] ?? ''}}">
                <div class="col-12">
                    <label for="name" class="form-label">Наименование</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $task['name'] ?? ''}}">
                </div>
                <div class="col-12">
                    <label for="tags" class="form-label">Теги</label>
                    <input name='tags' id="tags" value='{{ $task['tags'] ?? ''}}' class="form-control" autofocus>
                </div>


                <div class="col-12">
                    <div class="row imageBlock">

                        <div class="col-md-6">
                            <input type="file" id="imageInput" class="form-control" >
                            <input type="hidden" name="image" class="form-control" value='{{ $task['image'] ?? ''}}'>
                            <input type="hidden" name="resized_image" class="form-control" value='{{ $task['resized_image'] ?? ''}}'>
                        </div>

                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-success" name="uploadImage">Загрузить</button>
                        </div>
                        <br/>
                        <div class="thumbImageWrapper">
                            @isset($task['resized_image'])
                                <a href="{{ URL::to('/').'/'.$task['image'] }}" target="_blank"><img
                                        src="{{ URL::to('/').'/'.$task['resized_image'] }}"
                                        class="img-thumbnail mt-2" alt=""></a><br>
                                <button class="btn btn-sm btn-outline-secondary mt-2" type="button" name="deleteImage">
                                    Удалить
                                </button>
                            @endisset

                        </div>
                    </div>
                </div>

                {{--            <div class="card">--}}
{{--                <div class="card-header">{{ __('Dashboard') }}</div>--}}

{{--                <div class="card-body">--}}
{{--                    @if (session('status'))--}}
{{--                        <div class="alert alert-success" role="alert">--}}
{{--                            {{ session('status') }}--}}
{{--                        </div>--}}
{{--                    @endif--}}

{{--                    {{ __('You are logged in!') }}--}}
{{--                </div>--}}
{{--            </div>--}}
                <div class="container-fluid d-flex justify-content-end p-0 mt-3">
                    <a class="btn btn-outline-secondary me-2" href="{{ url('/') }}">Отменить</a>
                    @isset($task['id'])
                        <button class="btn btn-outline-danger me-2" type="button" name="deleteTask" data-id="{{ $task['id'] }}">Удалить</button>
                    @endisset
                    <button class="btn btn-outline-success me-2" type="button" name="saveTask">Сохранить</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
