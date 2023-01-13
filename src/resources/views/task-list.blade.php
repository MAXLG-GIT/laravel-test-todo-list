@forelse ($taskList as $task)
    <a class="list-group-item list-group-item-action" data-id='{{$task->id}}'
       id="task-{{$task->id}}" data-bs-toggle="list" href="#" role="tab"
       aria-controls="list-home">{{$task->name}}</a>

@empty
    <p>Нет записей</p>
@endforelse
