@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="container-fluid d-flex justify-content-between p-0">


                    @isset($tagList)
                        <div class="dropdown d-flex mb-3">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
                                Теги
                            </button>
                            <div class="dropdown-menu">
                                <div class="list-group list-group-flush">
                                    @foreach ($tagList as $tag)
                                        <label class="list-group-item">
                                            <input class="form-check-input me-1" type="checkbox" value="{{$tag->id}}" name="tagsFilter">
                                            {{$tag->name}}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endisset

                    <div class="d-flex mb-3 ">
                        <input class="form-control me-2" type="search" placeholder="Поиск" aria-label="Search" name="searchField">
                        <button class="btn btn-outline-success" type="submit" name="searchBtn">Искать</button>
                    </div>
                </div>


                <div class="messageWrapper"></div>
                <div class="list-group" id="taskList" role="tablist">
                    @include('task-list')
                </div>


                <div class="container-fluid d-flex justify-content-end p-0 mt-3">
                    <button class="btn btn-outline-primary me-2" name="editTask" disabled>Изменить</button>
                    <button class="btn btn-outline-danger me-2" name="deleteTask" disabled>Удалить</button>
                    <a class="btn btn-outline-success" href="{{ url('/edit') }}">Добавить</a>

                </div>

            </div>
        </div>
    </div>
@endsection
