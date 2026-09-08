<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessVideoJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\photo;
use App\Photocommente;
use App\User;
use App\Video;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
//use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
//use FFMpeg\Format\Video\X264;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;

class PhotoController extends Controller
{
    //
    public function img($photos)
    {
        
        $allowedTypes = [
            'image/jpeg','image/jpg','image/png','image/gif','image/webp','video/mp4','video/avi',
            'video/mkv','video/mov','video/quicktime','video/wmv','video/flv','video/webm','video/mpeg','video/3gpp',
        ];
        $maxSizeInBytes = 200 * 1024 * 1024;


        if(is_array($photos)){
            $PhotoArr=[];
            $VideoArr=[];
            $resu=[];
            $ttype=[];
            foreach($photos as $photo){
                if(!in_array($photo->getMimeType(), $allowedTypes)){
                    return response()->json(data:[
                        'error' => 'failed',
                        'details' => $photo->getClientOriginalName().'Invalid image type. Only JPEG or JPG images are allowed'
                    ]);
                }else if($photo->getSize() > $maxSizeInBytes){
                    return response()->json([
                        'error' => 'failed',
                        'details' => $photo->getClientOriginalName().' exceeds the maximum allowed size of 200 MB'
                    ]);
                }else{
                    if(strpos($photo->getMimeType(), 'video') !== false ){
                        $VideoArr[] = $photo;
                        
                    }else{
                        $PhotoArr[] = $photo;
                    }
                    }
        
        }
        if(!empty($VideoArr)){

          $resu['video']=  $this->upluad_vid($VideoArr,$VideoArr);
          $ttype[]=["video"];
        }
        if(!empty($PhotoArr)){
            $resu['photo']=  $this->upluad_img($PhotoArr,$PhotoArr);
            $ttype[]=["img"];
        }
        
        return response()->json([
                                'status' => 'ok',
                                'details' => $resu,
                                'type'=>$ttype
        ]);         
        }else{
            
            if(!in_array($photos->getMimeType(), $allowedTypes)){
                return response()->json(data:[
                    'error' => 'failed',
                    'details' => $photos->getClientOriginalName().'Invalid image type. Only JPEG or JPG images are allowed'
                ]);
            }else if($photos->getSize() > $maxSizeInBytes){
                return response()->json([
                    'error' => 'failed',
                        'details' => $photos->getClientOriginalName().' exceeds the maximum allowed size of 200 MB'
                ]);
            }else{
                $arr['name']    =   $photos->getClientOriginalName();
                $arr['path']    =   $photos->getPathName();
                $arr['size']    =   round(($photos->getSize()/1024))."M";
                $arr['type']    =   $photos->getMimeType();

                if(strpos($arr['type'], 'video') !== false ){
                    return  response()->json([
                        'status' => 'ok',
                        'details' => $this->upluad_vid([$photos],[$photos]),
                        'type'=>"video"
                    ]);  
                    
                }else{
                    return  response()->json([
                                    'status' => 'ok',
                                    'details' => $this->upluad_img([$photos],[$photos]),
                                    'type'=>"img"
                                ]);  
                }

            }
                 
        }
         
    }
    public function upluad_img($imgs,$photos)
    {

         foreach($imgs as $key =>$im){  
            $extension = $this->safeExtension($photos[$key]);
            $type   =   '.'.$extension;
            $photo =   photo::create([
                'user_id' => auth()->id(),
                'path'    => 'images/users/'.auth()->id().'/', // Assign the temporary user id
                'state'   => 1,
                'album_id'=> 0,
                'type'    => $type,

            ]);
            if ($photo) {
            $directory = public_path($photo->path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $photos[$key]->move($directory, $photo->id.$type);
            }

            $img[$key]=array($photo->id);
        
        }
        
        return json_encode($img,JSON_FORCE_OBJECT);

    }
    public function upluad_vid($imgs,$videos)
    {

         foreach($imgs as $key =>$im){  
            $type   =   '.'.$this->safeExtension($videos[$key]);
            $video =   Video::create([
                'user_id' => auth()->id(),
                'path'    => 'video/users/'.auth()->id().'/', // Assign the temporary user id
                'state'   => 1,
                'album_id'=> 0,
                'type'    => $type,
            ]);
            if ($video) {
            $directory = public_path($video->path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            $videos[$key]->move($directory, $video->id.$type);
            }

            $img[$key]=array($video->id);
        // $this->ffmpegpro($video->path,$video->id,$type);
         ProcessVideoJob::dispatch($video->path, $video->id, $type);

        }
       
        //echo public_path($video->path);
        return json_encode($img,JSON_FORCE_OBJECT);

    }

    private function safeExtension(UploadedFile $file): string
    {
        return match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/quicktime', 'video/mov' => 'mov',
            'video/avi' => 'avi',
            'video/mkv' => 'mkv',
            'video/wmv' => 'wmv',
            'video/flv' => 'flv',
            'video/mpeg' => 'mpeg',
            'video/3gpp' => '3gp',
            default => throw new \InvalidArgumentException('Unsupported media type.'),
        };
    }
    // public function ffmpegpro($path, $name, $type)
    // {
    //     set_time_limit(0); 

    //     $outputDir = public_path($path . $name . "/");
    //     $inputFile = public_path($path . $name . $type);
    
    //     // التأكد من وجود المجلد
    //     if (!file_exists($outputDir)) {
    //         mkdir($outputDir, 0777, true);
    //     }
    
    //     try {
    //         // تهيئة FFMpeg
    //         $ffmpeg = FFMpeg::create([
    //             'ffmpeg.binaries'  => env('FFMPEG_BINARIES', 'C:/xampp8/htdocs/facebook-laravel/bin/ffmpeg.exe'),
    //             'ffprobe.binaries' => env('FFPROBE_BINARIES', 'C:/xampp8/htdocs/facebook-laravel/bin/ffprobe.exe'),
    //             'timeout'          => 3600,
    //             'ffmpeg.threads'   => 4,
    //         ]);
    
    //         $video = $ffmpeg->open($inputFile);
    
    //         // تعريف الجودات المطلوبة
    //         $qualities = [
    //             '1080p' => [1920, 1080, 5000],
    //             '720p'  => [1280, 720, 2800],
    //             '480p'  => [854, 480, 1400],
    //             '360p'  => [640, 360, 800],
    //             '240p'  => [426, 240, 500],
    //             '144p'  => [256, 144, 300],
    //         ];
    
    //         $masterPlaylist = "#EXTM3U\n";
    
    //         foreach ($qualities as $quality => $settings) {
    //             list($width, $height, $bitrate) = $settings;
    
    //             $format = new X264('aac', 'libx264');
    //             $format->setKiloBitrate($bitrate);
    //             $format->setAudioKiloBitrate(128);
    
    //             // إنشاء مسار إخراج ملف m3u8 لكل جودة
    //             $outputPath = $outputDir . $name . '_' . $quality . ".m3u8";
    
    //             // تطبيق الفلتر وحفظ الملف
    //             $video->filters()->resize(new Dimension($width, $height))
    //                 ->synchronize();
    
    //             $video->save($format, $outputPath);
    
    //             // إنشاء قائمة تشغيل للجودة المحددة
    //             $individualPlaylist = "#EXTM3U\n";
    //             $individualPlaylist .= "#EXT-X-STREAM-INF:BANDWIDTH=" . ($bitrate * 1000) . ",RESOLUTION={$width}x{$height}\n";
    //             $individualPlaylist .= $name . '_' . $quality . ".ts\n";
    //             file_put_contents($outputDir . $name . '_' . $quality . ".m3u8", $individualPlaylist);
    
    //             // إضافة الرابط إلى قائمة التشغيل الرئيسية
    //             $masterPlaylist .= "#EXT-X-STREAM-INF:BANDWIDTH=" . ($bitrate * 1000) . ",RESOLUTION={$width}x{$height}\n";
    //             $masterPlaylist .= $name . '_' . $quality . ".m3u8\n";
    //         }
    
    //         // حفظ قائمة التشغيل الرئيسية
    //         file_put_contents($outputDir . "playlist.m3u8", $masterPlaylist);
    
    //     } catch (Exception $e) {
    //         echo "حدث خطأ: " . $e->getMessage();
    //         error_log("Error processing video: " . $e->getMessage(), 3, "error_log.txt");
    //     }
    // }
    
    public function photo($id)
    {
        $photo  =   photo::where('id',$id)->with('user','photocommentes','reactphoto')->firstOrFail();
        $commente=   Photocommente::where('photo_id',$id)->with('user','reply')->get();
        if (auth()->check()) {
            $profilee = new UsersController;
            $profile = $profilee->profilenav();
        } else {
            $profile = null;
        }
        return view('/photo',compact('photo','commente','profile'));

    }
    public function profile_pic(Request $request){
            $request->validate(['files'=>'required|image|mimes:jpg,jpeg,png,webp|max:10240','cover'=>'nullable|boolean']);
            $userId = auth()->id();
            if ($request->hasFile('files')) {
                $photo = $request->file('files');
            $lol= $this->img([$photo]);
             $data = $lol->getData(true);
            $photoId=json_decode($data['details']['photo'],true)[0][0];
            
            if(empty($request->input('cover'))){
            $user = User::where('id',$userId)->firstOrFail();
            $user->profile_photo_id = $photoId ;
            $user->save();
            $editphoto = photo::where('id',$photoId )->firstOrFail();
            $editphoto->album_id = 1;
            $editphoto->save();
            return response()->json(['profile'=>asset($editphoto->path.$editphoto->id.$editphoto->type)]);
            }else{
            $user = User::where('id',$userId)->firstOrFail();
            $user->cover_photo_id = $photoId ;
            $user->save();
            $editphoto = photo::where('id',$photoId )->firstOrFail();
            $editphoto->album_id = 1;
            $editphoto->save();
            return response()->json(['cover'=>asset($editphoto->path.$editphoto->id.$editphoto->type)]);
            }
        }else{
            return response()->json(['message' => 'Image file is required.'], 422);
        }
    }
   
}
