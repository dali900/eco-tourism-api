<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Http\Resources\VideoFileResource;
use App\Contracts\VideoRepositoryInterface;
use App\Http\Resources\VideoResourcePaginated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Video;
use App\Models\VideoFile;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VideoController extends Controller
{
    /**
     * VideoRepository
     *
     * @var VideoRepository
     */
    private $videoRepository;

    public function __construct(VideoRepositoryInterface $videoRepository) {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($app, $id)
    {
        $video = Video::find($id);
        if(!$video){
            return $this->responseNotFound();
        }
        return $this->responseSuccess([
            'video' => VideoResource::make($video)
        ]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request, $app)
    {
        $perPage = $request->perPage ?? 20;
        $videos = $this->videoRepository->getAllFiltered($request->all(), $app);
        $videoPaginated = $videos->paginate($perPage);
        $videoResource = VideoResourcePaginated::make($videoPaginated);
        return $this->responseSuccess([
            'videos' => $videoResource
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $app)
    {
        $attr = $request->validate([
			'video_link' => 'required|string'
		]);

        $user = auth()->user();

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        $video = Video::create($data);

        //Upload files
        if(!empty($data['files'])){
            foreach ($data['files'] as $file) {
                $videoFile = VideoFile::create([
                    'user_id' => $user->id,
                    'video_id' => $video->id,
                    'file_path' => $file['file_path'],
                    'file_name' => $file['original_name']
                ]);

                $extension = pathinfo(base_path().'/'.$file['file_path'], PATHINFO_EXTENSION);
                $newPath = 'video_files/'.$videoFile->id.'_'.Str::random(6).'.'.$extension;
                Storage::move($file['file_path'], $newPath);
                $videoFile->update([
                    'file_path' => $newPath
                ]);
            }
        }

        return $this->responseSuccess([
            'video' => VideoResource::make($video)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $app, $id)
    {
        $attr = $request->validate([
			'video_link' => 'required|string'
		]);

        $user = auth()->user();

        $video = Video::find($id);
        if(!$video){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['app'] = $app;
        $data['publish_date'] = !empty($data['publish_date']) ? Carbon::createFromFormat('d.m.Y.', $data['publish_date']) : date('Y-m-d H:i:s');

        $video->update($data);

        //delete old video files
        $oldVideoFiles = VideoFile::where('video_id', $video->id)->get();
        foreach ($oldVideoFiles as $oldVideoFile) {
            $oldVideoFile->delete();
        }

        //Upload new video files
        if(!empty($data['files'])){
            foreach ($data['files'] as $file) {
                $videoFile = VideoFile::create([
                    'user_id' => $user->id,
                    'video_id' => $video->id,
                    'file_path' => $file['file_path'],
                    'file_name' => $file['original_name']
                ]);

                $extension = pathinfo(base_path().'/'.$file['file_path'], PATHINFO_EXTENSION);
                $newPath = 'video_files/'.$videoFile->id.'_'.Str::random(6).'.'.$extension;
                Storage::move($file['file_path'], $newPath);
                $videoFile->update([
                    'file_path' => $newPath
                ]);
            }
        }

        return $this->responseSuccess([
            'video' => VideoResource::make($video)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($app, $id)
    {
        $video = Video::find($id);
        if(!$video){
            return $this->responseNotFound();
        }

        //delete video files
        $oldVideoFiles = VideoFile::where('video_id', $video->id)->get();
        foreach ($oldVideoFiles as $oldVideoFile) {
            $oldVideoFile->delete();
        }

        $video->delete();
        return $this->responseSuccess();
    }

    public function downloadVideoFile($app, $id)
    {
        $videoFile = VideoFile::find($id);
        if(!$videoFile){
            return $this->responseNotFound();
        }

        $filePath = storage_path("/app/".$videoFile->file_path);
        $filePathParts = explode(".", $videoFile->file_path);
        $downloadName = $videoFile->file_name;
        return response()->download($filePath, $downloadName, ['Download-Name' => $downloadName, "Access-Control-Expose-Headers" => "Download-Name"]);
    }
}
