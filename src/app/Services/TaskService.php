<?php declare(strict_types=1);

namespace App\Services;
use App\Models\Task;
use App\Models\Tag;
use App\Models\Image;
use App\Services\ImageService;
use App\Services\TagsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Request;
use Log;

class TaskService
{
    protected int $userId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected \Illuminate\Http\Request $request)
    {
        $this->userId = Auth::id() ?? 0;
    }

    /**
     * Save task.
     *
     * @return Collection of saving status
     */
    public function save():Collection
    {
        if (isset($this->request->id)) {

            $task = Task::find($this->request->id);
            $task->tags()->delete();

        } else{
            $task = new Task;
            $task->user_id = $this->userId;
        }

        try {
            $tagsArray = [];
            $task->name = $this->request->name ?? '';
            if (isset($this->request->tags)){

                foreach (json_decode($this->request->tags) as $requestTag) {
                    $tag = Tag::firstOrCreate([
                        'name' => $requestTag->value,
                        'user_id' => $this->userId
                    ]);
                    $tagsArray[] =  $tag->id;
                }
            }
            $task->save();
            $task->tags()->sync($tagsArray);
            if (isset($this->request->image)){
                $task->image()->delete();
                $task->image()->create(['image_link'=> $this->request->image,
                      'resized_image_link'=> $this->request->resized_image]);
            }
            return collect(['status' => true, 'id' => $task->id, 'message' => __('todo.task_saved')]);
        }catch(\Illuminate\Database\QueryException $exception ){
            Log::error(json_encode($exception));
            return collect(['status' => false, 'message' => json_encode($exception)]);
        }
    }


    /**
     * Delete task.
     *
     * @return Collection of saving status
     * TODO combine tags and images removing in deleteTask() and save() methods
     */
    public function deleteTask():Collection
    {
        try {
            $task = Task::find($this->request->id);
            $task->tags()->delete();
            $task->image()->delete();
            $task->delete();

            return collect(['status' => true, 'id' => $this->request->id, 'message' => __('todo.task_saved')]);
        }catch(\Illuminate\Database\QueryException $exception ){
            Log::error(json_encode($exception));
            return collect(['status' => false, 'message' => json_encode($exception)]);
        }
    }

    /**
     * Retrieve task fields.
     *
     * @return array of task fields
     */
    public function retrieveTaskFields():array
    {
        $taskData = [];
        try {
            $task = Task::find($this->request->id);
            $taskData['id'] = $task->id;
            $taskData['name'] = $task->name;
            $taskTags = $task->tags()->select('name')->get() ?? [] ;
            foreach ($taskTags as $taskTag){
                $taskData['tags'][] = $taskTag->name;
            }
            $taskData['tags'] = implode(',',  $taskData['tags']);
            $taskData['image'] = $task->image()->select('image_link')->first()->image_link ?? '' ;
            $taskData['resized_image'] =$task->image()->select('resized_image_link')->first()->resized_image_link ?? '' ;

            return $taskData;
        }catch(\Illuminate\Database\QueryException $exception ){
            Log::error(json_encode($exception));
            return [];
        }
    }
}
