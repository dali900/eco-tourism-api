<?php

namespace App\Http\Resources\Article;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use App\Http\Resources\FileResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
			'title' => $this->title,
			'author' => $this->author,
			'user_id' => $this->user_id,
			'user' => UserResource::make($this->whenLoaded('user')),
			'publish_date' => $this->publish_date,
			'publish_date_formated' => $this->publish_date ? Carbon::parse($this->publish_date)->format("d.m.Y.") : null,
			'pdf_file' => FileResource::make($this->whenLoaded('pdfFile')),
			'html_files' => FileResource::collection($this->whenLoaded('htmlFiles')),
			'html_file' => FileResource::make($this->whenLoaded('htmlFile')),
			'html_file_content' => $this->whenLoaded('htmlFile') ? file_get_contents(storage_path().'/app/'.$this->htmlFile->file_path) : null,
			'download_file' => FileResource::make($this->whenLoaded('downloadFile')),
			'article_type_id' => $this->article_type_id,
			'article_type' => ArticleTypeResource::make($this->whenLoaded('articleType')),
			'approved' => $this->approved
		];
    }

    /**
	 * Get html document from word doc
	 *
	 * @param string $filePath
	 * @return string
	 */
	public function getHtmlDoc($filePath) : ?string
	{
		if($this->isWordDoc($filePath)){
			$phpWord = \PhpOffice\PhpWord\IOFactory::load(storage_path().'/app/'.$filePath);
			$htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
			return $htmlWriter->getContent();
		} else if($this->isHtmlDoc($filePath)){
			return file_get_contents(storage_path().'/app/'.$filePath);
		}
		return "";
	}

    public function isWordDoc($path)
	{
		if(!$path) return false;
		$extension = pathinfo(storage_path().'/app/'.$path, PATHINFO_EXTENSION);
		if(
			$extension == 'docx' || 
			$extension == 'dotx'
			//$extension == 'doc' || //not fully supported
			//$extension == 'dot' || //not fully supported
			//$extension == 'odt' || 
			//$extension == 'docm' || 
			//$extension == 'rtf' || //not fully supported
			//$extension == 'uot' || 
			//$extension == 'xml' || 
			//$extension == 'html' ||
			//$extension == 'txt'
		) {
			return true;
		}
		return false;
	}
	
	public function isHtmlDoc($path)
	{
		if(!$path) return false;
		$extension = pathinfo(storage_path().'/app/'.$path, PATHINFO_EXTENSION);
		if($extension == 'html') {
			return true;
		}
		return false;
	}
}
