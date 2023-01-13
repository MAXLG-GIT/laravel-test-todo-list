<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use App\Services\TaskService;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Log;


class TaskController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Get tasks assigned to user.
     *
     * @return View
     */
    public function listPage():View
    {
        $taskList = Task::where('user_id', Auth::id() ?? 0)->get();
        $tagList = Tag::where('user_id', Auth::id() ?? 0)->get();
        return view('main', [
            'taskList' => $taskList,
            'tagList' => $tagList,
        ]);
    }

    /**
     * Get tasks, filtered by tag.
     * @param  \Illuminate\Http\Request $request
     * @return View
     */
    public function tagsFilter(\Illuminate\Http\Request $request):string
    {
        if (count($request->checked_tags_arr ) > 0)
            $taskList = Task::select('id', 'name')
                ->whereHas('tags', function (Builder $query) use ($request){
                    $query->whereIn('tags.id',$request->checked_tags_arr);
                })
                ->where('user_id', Auth::id() ?? 0)
                ->get();
        else
            $taskList = Task::select('id', 'name')
                ->where('user_id', Auth::id() ?? 0)
                ->get();

        return json_encode(['status'=> true, 'content' => view('task-list', [
            'taskList' => $taskList,
        ])->render()]);

    }

    /**
     * Get tasks, filtered search strings.
     * @param  \Illuminate\Http\Request $request
     * @return View
     */
    public function search(\Illuminate\Http\Request $request):string
    {
        if ($request->search_string && strlen($request->search_string ) > 0)
            $taskList = Task::select('id', 'name')
                ->where('name','LIKE',"%{$request->search_string}%")
                ->where('user_id', Auth::id() ?? 0)
                ->get();
        else
            $taskList = Task::select('id', 'name')
                ->where('user_id', Auth::id() ?? 0)
                ->get();

        return json_encode(['status'=> true, 'content' => view('task-list', [
            'taskList' => $taskList,
        ])->render()]);

    }

    /**
     * Show edit page.
     * @param  \Illuminate\Http\Request $request
     * @return View
     */
    public function editPage(\Illuminate\Http\Request $request):View
    {
        $taskData =[];
        if (isset($request->id)){
            $taskService = new TaskService($request);
            $taskData = $taskService->retrieveTaskFields();
        }

        return view('task-edit', [
            'task' => $taskData,
        ]);
    }

    /**
     * Save task.
     * @param  \Illuminate\Http\Request $request AJAX request with task data to save
     * @return bool|string JSON of successfull task saving or not
     */
    public function saveTask(\Illuminate\Http\Request $request):bool|string
    {

        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'name' => 'required|string|max:1000',
            'tags' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:1000',
            'image_resized' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return json_encode(['status'=> false, 'message' => __('todo.task_invalid_data')]);
        }

        $taskService = new TaskService($request);

        $savingResult = $taskService->save();
//        if ($request->hasFile('image')) {
//            $request->file('image')->move(public_path('images') . 'temp');
//        }
        return json_encode($savingResult);
    }


    /**
     * Upload task image.
     *  @param  \Illuminate\Http\Request $request AJAX request image to upload
     * @return bool|string JSON of successful uploaded image
     */
    public function uploadImage(\Illuminate\Http\Request $request):bool|string
    {

        $validator = Validator::make($request->all(), [
            'file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return json_encode(['status'=> false, 'message' => __('todo.image_invalid')]);
        }

        $imageService = new ImageService($request);
        $uploadResult = $imageService->imageUploadPost();

        return json_encode($uploadResult);
    }

    /**
     * Delete task image.
     *  @param  \Illuminate\Http\Request $request AJAX request image to upload
     * @return bool|string JSON on successful image removal
     */
    public function deleteImage(\Illuminate\Http\Request $request):bool|string
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|string|max:1000',
            'image_resized' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return json_encode(['status'=> false, 'message' => __('todo.image_removing_error')]);
        }

        $imageService = new ImageService($request);
        $removingResult = $imageService->removeImage();

        return json_encode($removingResult);
    }

    /**
     * Delete task .
     *  @param  \Illuminate\Http\Request $request AJAX request image to upload
     * @return bool|string JSON on successful image removal
     */
    public function deleteTask(\Illuminate\Http\Request $request):bool|string
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return json_encode(['status'=> false, 'message' => __('todo.task_removing_error')]);
        }

        $taskService = new TaskService($request);
        $removingResult = $taskService->deleteTask();

        return json_encode($removingResult);
    }

}
