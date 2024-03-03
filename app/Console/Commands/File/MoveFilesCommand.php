<?php

namespace App\Console\Commands\File;

use App\Models\Article;
use App\Models\Document;
use App\Models\File;
use App\Models\News;
use App\Models\Regulation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:move-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move old files to files table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line("Moving files...");
        $this->newLine();
        
        //Documents
        $this->line("Documents");
        $invalidFilePaths = [];
        $total = Document::count();
        $chunks = $total / 100;
        $totalChunks = $chunks > 1 ? $chunks : 1;
        $bar = $this->output->createProgressBar($total);
        $this->line("Chunks: $totalChunks");
        $this->line("total: $total");
        $documents = Document::get();
        foreach ($documents as $document) {
            $bar->advance();
            //PDF
            $file_path = $document->preview_file_path;
            if(!empty($file_path)) {
                $document->files()->delete();
                $file_path = 'public'.$document->preview_file_path;
                $newPath = str_replace('document-previews', 'documents', $file_path);
                if(Storage::exists($file_path)){
                    Storage::move($file_path, $newPath);
                } else {
                    $invalidFilePaths[] = $file_path;
                }
                $fileData = [
                    'original_name' => pathinfo(base_path().'/'.$newPath, PATHINFO_BASENAME),
                    'ext' => pathinfo(base_path().'/'.$newPath, PATHINFO_EXTENSION),
                    'file_path' => $newPath,
                    'user_id' => $document->user_id,
                    'is_tmp' => false,
                    'file_tag' => File::TAG_PDF_PREVIEW,
                    'is_public' => true,
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at
                ];
                $document->files()->create($fileData);
                $document->preview_file_path = null;
                $document->save();
            }
            //Download file
            $file_path = $document->file_path;
            if(!empty($file_path)) {
                $fileData = [
                    'original_name' => pathinfo(base_path().'/'.$file_path, PATHINFO_BASENAME),
                    'ext' => pathinfo(base_path().'/'.$file_path, PATHINFO_EXTENSION),
                    'file_path' => $file_path,
                    'user_id' => $document->user_id,
                    'is_tmp' => false,
                    'file_tag' => File::TAG_DOWNLOAD_FILE,
                    'is_public' => false,
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at
                ];
                $document->files()->create($fileData);
                $document->file_path = null;
                $document->save();
            }
        }
        Schema::table('documents', function (Blueprint $table) {
            $table->dropcolumn('file_path');
            $table->dropcolumn('preview_file_path');
        });
        Storage::deleteDirectory('public/document-previews');
        $this->newLine();

        if(!empty($invalidFilePaths)){
            $this->warn("Invalid file paths:");
            foreach ($invalidFilePaths as $filePath) {
                $this->line($filePath);
            }
        }
        $this->newLine();
        //Regulations
        $this->line("Regulations");
        $invalidFilePaths = [];
        $total = Regulation::count();
        $chunks = $total / 100;
        $totalChunks = $chunks > 1 ? $chunks : 1;
        $bar = $this->output->createProgressBar($total);
        $this->line("Chunks: $totalChunks");
        $this->line("total: $total");
        $regulations = Regulation::get();
        foreach ($regulations as $regulation) {
            $bar->advance();
            //PDF
            $file_path = $regulation->preview_file_path;
            if(!empty($file_path)) {
                $regulation->files()->delete();
                $file_path = 'public'.$regulation->preview_file_path;
                $newPath = str_replace('regulation-previews', 'regulations', $file_path);
                if(Storage::exists($file_path)){
                    Storage::move($file_path, $newPath);
                } else {
                    $invalidFilePaths[] = $file_path;
                }
                $fileData = [
                    'original_name' => pathinfo(base_path().'/'.$newPath, PATHINFO_BASENAME),
                    'ext' => pathinfo(base_path().'/'.$newPath, PATHINFO_EXTENSION),
                    'file_path' => $newPath,
                    'user_id' => $regulation->user_id,
                    'is_tmp' => false,
                    'file_tag' => File::TAG_PDF_PREVIEW,
                    'is_public' => true,
                    'created_at' => $regulation->created_at,
                    'updated_at' => $regulation->updated_at
                ];
                $regulation->files()->create($fileData);
                $regulation->preview_file_path = null;
                $regulation->save();
            }
            //Download file
            $file_path = $regulation->file_path;
            if(!empty($file_path)) {
                $fileData = [
                    'original_name' => pathinfo(base_path().'/'.$file_path, PATHINFO_BASENAME),
                    'ext' => pathinfo(base_path().'/'.$file_path, PATHINFO_EXTENSION),
                    'file_path' => $file_path,
                    'user_id' => $regulation->user_id,
                    'is_tmp' => false,
                    'file_tag' => File::TAG_DOWNLOAD_FILE,
                    'is_public' => false,
                    'created_at' => $regulation->created_at,
                    'updated_at' => $regulation->updated_at
                ];
                $regulation->files()->create($fileData);
                $regulation->file_path = null;
                $regulation->save();
            }
        }
        $this->newLine();
        if(!empty($invalidFilePaths)){
            $this->warn("Invalid file paths:");
            foreach ($invalidFilePaths as $filePath) {
                $this->line($filePath);
            }
        }
        Schema::table('regulations', function (Blueprint $table) {
            $table->dropcolumn('file_path');
            $table->dropcolumn('preview_file_path');
        });
        Storage::deleteDirectory('public/regulation-previews');
        $this->newLine();

        //Articles
        $this->line("Articles");
        $invalidFilePaths = [];
        $total = Article::count();
        $chunks = $total / 100;
        $totalChunks = $chunks > 1 ? $chunks : 1;
        $bar = $this->output->createProgressBar($total);
        $this->line("Chunks: $totalChunks");
        $this->line("total: $total");
        $articles = Article::get();
        foreach ($articles as $article) {
            $bar->advance();
            //PDF
            $file_path = $article->preview_file_path;
            if(!empty($file_path)) {
                $article->files()->delete();
                $file_path = 'public'.$article->preview_file_path;
                $newPath = str_replace('article-previews', 'articles', $file_path);
                if(Storage::exists($file_path)){
                    Storage::move($file_path, $newPath);
                } else {
                    $invalidFilePaths[] = $file_path;
                }
                $fileData = [
                    'original_name' => pathinfo(base_path().'/'.$newPath, PATHINFO_BASENAME),
                    'ext' => pathinfo(base_path().'/'.$newPath, PATHINFO_EXTENSION),
                    'file_path' => $newPath,
                    'user_id' => $article->user_id,
                    'is_tmp' => false,
                    'file_tag' => File::TAG_PDF_PREVIEW,
                    'is_public' => true,
                    'created_at' => $article->created_at,
                    'updated_at' => $article->updated_at
                ];
                $article->files()->create($fileData);
                $article->preview_file_path = null;
                $article->save();
            }
        }
        $this->newLine();
        if(!empty($invalidFilePaths)){
            $this->warn("Invalid file paths:");
            foreach ($invalidFilePaths as $filePath) {
                $this->line($filePath);
            }
        }
        Schema::table('articles', function (Blueprint $table) {
            $table->dropcolumn('file_path');
            $table->dropcolumn('preview_file_path');
        });
        Storage::deleteDirectory('public/article-previews');
        $this->newLine();

        //News
        $this->line("News");
        $total = News::count();
        $chunks = $total / 100;
        $totalChunks = $chunks > 1 ? $chunks : 1;
        $bar = $this->output->createProgressBar($total);
        $this->line("Chunks: $totalChunks");
        $this->line("total: $total");
        $news = News::get();
        foreach ($news as $post) {
            $bar->advance();
            $file_path = $post->file_path;
            if(!empty($file_path)) {
                $post->image()->delete();
                $file_path = $post->file_path;
                $fileData = [
                    'original_name' => pathinfo(base_path().'/'.$file_path, PATHINFO_BASENAME),
                    'ext' => pathinfo(base_path().'/'.$file_path, PATHINFO_EXTENSION),
                    'file_path' => $file_path,
                    'user_id' => $post->user_id,
                    'is_tmp' => false,
                    'file_tag' => null,
                    'is_public' => true,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at
                ];
                $post->files()->create($fileData);
                $post->file_path = null;
                $post->save();
            }
        }
        Schema::table('news', function (Blueprint $table) {
            $table->dropcolumn('file_path');
        });
        $this->newLine();
    }
}
