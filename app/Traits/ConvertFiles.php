<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Handle logic for moving and saving temp files
 */
trait ConvertFiles
{

    /**
     * Converts all word document to hmtl if new word document has been uploaded
     *
     * @param array $params
     * @param string $storageFolderPath 
     * @return self
     */
    public function convertWordFileToPdf($files, $storageFolder)
    {
        //$process = Process::fromShellCommandline("unoconv -h");
        $process = Process::fromShellCommandline("libreoffice -h");
        $process->run();

        // executes after the command finishes
        if ($process->isSuccessful()) {
            foreach ($files as $file) {
                $fileTag = $file['file_tag'] ?? null;
                if ($fileTag == File::TAG_DOWNLOAD_FILE) {
                    $wordFile = $this->downloadFile;
                    if ($wordFile && in_array($wordFile->ext, ['docx', 'dotx', 'doc', 'odt', 'dot'])) {
                        $this->deleteAllHtmlFiles();
                        $this->htmlFiles()->delete();
                        $this->convertAndMoveFile($wordFile, $storageFolder);
                        //Artisan::call("convert:word-to-html $wordFilePath $storageFolder");
                    }
                    return $this;
                }
            }
        } else {
            /* logger()->error("missing unoconv, run: 'sudo apt-get install unoconv'");
            abort(500, "missing unoconv, run: sudo apt-get install unoconv"); */
            logger()->error("missing libreoffice, run: 'sudo apt install libreoffice -y'");
            abort(500, "missing libreoffice, run: sudo apt install libreoffice -y");
        }
        return $this;
    }

    private function convertAndMoveFile($wordFile, $storageFolder)
    {
        $user = auth()->user();
        //dodati nove html fajlove u files
        $fullFilePath = storage_path() . '/app/' . $wordFile->file_path;
        $storageFilePath = $wordFile->file_path;
        $storageFolderPath = '/public/' . $storageFolder; //place to move uploaded files

        $fileData = [];
        $targetFolderName = Str::random(6);
        Storage::makeDirectory('tmp/' . $targetFolderName, 777);
        $targetFullFolderPath = storage_path() . "/app/tmp/$targetFolderName";
        $targetFileBaseName = pathinfo($fullFilePath, PATHINFO_BASENAME);
        Storage::copy($storageFilePath, 'tmp/' . $targetFolderName . '/' . $targetFileBaseName);
        $targetFilePath = $targetFullFolderPath . '/' . $targetFileBaseName;
        /* chmod($targetFullFolderPath, 0777);
        chmod($targetFilePath, 0777);
        putenv('HOME=/tmp');  */

        //convert
        //$process = Process::fromShellCommandline("unoconv -f html $targetFilePath");
        $process = Process::fromShellCommandline("/usr/bin/soffice --headless -env:UserInstallation=file:///tmp/test --convert-to html $targetFilePath --outdir $targetFullFolderPath");
        // add --backtrace and check /amp-api/gdbtrace.log file
        $process->run();

         // executes after the command finishes
        if (!$process->isSuccessful()) {
            logger($process->getOutput());
            throw new ProcessFailedException($process);
        }

        //logger($process->getOutput());
        //echo $process->getOutput();

        Storage::delete("tmp/$targetFolderName/$targetFileBaseName");
        $files = Storage::files("tmp/$targetFolderName");
        foreach ($files as $targetFolderFilePath) {
            $extension = pathinfo(base_path() . '/' . $targetFolderFilePath, PATHINFO_EXTENSION);
            $newFileName = pathinfo(base_path() . '/' . $targetFolderFilePath, PATHINFO_FILENAME);
            $fileData[] = [
                'original_name' => $newFileName . '.' . $extension,
                'ext' => $extension,
                'file_path' => 'public/' . $storageFolder . $newFileName . '.' . $extension,
                'user_id' => $user ? $user->id : null,
                'is_tmp' => false,
                'file_tag' => File::TAG_HTML_PREVIEW,
                'is_public' => true
            ];
            if ($extension == 'html') {
                $htmlContent = $this->appendStyleTagToHtmlFile($targetFolderFilePath, $storageFolder);
                $htmlContent = $this->parceImgSrc($htmlContent, $storageFolder);
                //update file
                file_put_contents(storage_path() . '/app/' . $targetFolderFilePath, $htmlContent);
            }
            Storage::move($targetFolderFilePath, $storageFolderPath . '/' . $newFileName . '.' . $extension);
        }
        $this->files()->createMany($fileData);
        Storage::deleteDirectory("tmp/$targetFolderName");
    }

    private function appendStyleTagToHtmlFile($targetFolderFilePath, $storageFolder)
    {
        $link = '<link href="'.config('app.url').'/css/iframe.css" type="text/css" rel="stylesheet"></link>';
        return file_get_contents(storage_path() . '/app/' . $targetFolderFilePath) . $link;
    }

    private function parceImgSrc($htmlContent, $storageFolder) {
        $escapedForwardSlash = str_replace('/', '\/', config('app.url').'/'.Storage::url($storageFolder).'/');
        return preg_replace('(src=")', 'src="'.$escapedForwardSlash, $htmlContent);
    }


    /**
     * Get the value of storageFilePath
     */
    public function getStorageFilePath()
    {
        return $this->storageFilePath;
    }
}
