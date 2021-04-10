<?php
namespace App\Services;

use App\Models\File;
use App\Models\Project;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectFileService {

    public function get($project_id) {
        $files = Project::find($project_id)->files()
            ->whereNull('files.deleted_at')
            ->orderBy('files.created_at', 'asc')
            ->get();

        return $files;
    }

    public function detail($id) {
        $file = File::whereNull('deleted_at')
            ->where('id',$id)->first();
        return $file;
    }

    public function save($project_id, $uploaded_file) {
        try{
            $file = null;
            DB::transaction(function () use(&$file, $project_id, $uploaded_file){
                $project = Project::find($project_id);
                $file_path = $uploaded_file->store('projects');
                $filename = $uploaded_file->getClientOriginalName();
                $ext = $uploaded_file->extension();
                $size = Storage::size($file_path);
                $created_by = Auth::user()->id;
                $updated_by = $created_by;
                $file = new File(compact("filename","file_path","ext","size","created_by","updated_by"));
                $project->files()->save($file);
            });
            return $file;
        } catch(\Exception $e) {
            throw new Exception("Failed to save data", 500);
        }
    }

    public function delete($file_id) {
        try{
            $result = false;
            DB::transaction(function() use(&$result,$file_id){
                $file = File::find($file_id);
                if(Storage::delete($file->file_path)) {
                    $file->project()->detach();
                    $file->deleted_by = Auth::user()->id;
                    $file->deleted_at = Carbon::now();
                    $result = $file->save();
                }
            });
            return $result;
        } catch(\Exception $e) {
            throw new Exception("Failed to delete data", 500);
        }
    }

}
