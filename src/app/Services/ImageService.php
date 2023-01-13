<?php declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Models\Image as ImageModel;


class ImageService
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected \Illuminate\Http\Request $request)
    {

    }

    /**
     * Process uploaded image.
     *
     * @return Collection image and resized_mage links JSON
     */
    public function imageUploadPost():Collection
    {

        $imageName = time().'.'.$this->request->file->extension();
        $resizedImageName = time().'_resized.'.$this->request->file->extension();

        try
        {
            $this->request->file->storeAs('public/images', $imageName);
            Image::make($this->request->file)
                ->resize(150, 150, function ($constraint) {$constraint->aspectRatio();})
                ->save(storage_path('app/public/images/').$resizedImageName, 70);
            $result = collect([
                'status' => true,
                'image'=>  'storage/images/'.$imageName,
                'resizedImage'=>  'storage/images/'.$resizedImageName]);
        }catch(\Intervention\Image\Exception\NotReadableException $exception)
        {
            Log::error(json_encode($exception));
            $result = collect(['status' => false, 'message' => __('todo.image_upload_failed')]);
        }
        return $result;
    }

    /**
     * Remove images and it's db records.
     *
     * @return Collection of removing status
     */
    public function removeImage():Collection
    {
        try
        {
            ImageModel::where('image_link', $this->request->image)
                ->orWhere('resized_image_link', $this->request->image_resized)->delete();
            File::delete([$this->request->image, $this->request->image_resized]);
            $result = collect(['status' => true, 'message' => __('todo.image_removing_succeed')]);
        }catch(\Intervention\Image\Exception\NotReadableException $exception)
        {
            Log::error(json_encode($exception));
            $result = collect(['status' => false, 'message' => __('todo.image_removing_error')]);
        }
        return $result;
    }

}
