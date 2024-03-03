<?php

namespace App\Http\Resources\Regulation;

use App\Http\Resources\FileResource;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Models\Regulation;

class RegulationResource extends JsonResource
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
			'name' => $this->name,
			'user' => UserResource::make($this->whenLoaded('user')),
			'type' => RegulationTypeResource::make($this->whenLoaded('regulationType')),
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'created_at_formated' => $this->created_at ? Carbon::parse($this->created_at)->format("d.m.Y.") : null,
			'updated_at_formated' => $this->updated_at ? Carbon::parse($this->updated_at)->format("d.m.Y.") : null,
			'regulation_type_id' => $this->regulation_type_id,
            //'file_html_doc' => $this->getHtmlDoc($this->file_path),
			'messenger' => $this->messenger,
			'maker' => $this->maker,
			'validity_level' => $this->validity_level,
			'start_date' => $this->start_date,
			'start_date_formated' => $this->start_date ? Carbon::parse($this->start_date)->format("d.m.Y.") : null,
			'end_date' => $this->end_date,
			'end_date_formated' => $this->end_date ? Carbon::parse($this->end_date)->format("d.m.Y.") : null,
			'use_start_date' => $this->use_start_date,
			'use_start_date_formated' => $this->use_start_date ? Carbon::parse($this->use_start_date)->format("d.m.Y.") : null,
			'version_release_date' => $this->version_release_date,
			'version_release_date_formated' => $this->version_release_date ? Carbon::parse($this->version_release_date)->format("d.m.Y.") : null,
			'version_end_date' => $this->version_end_date,
			'version_end_date_formated' => $this->version_end_date ? Carbon::parse($this->version_end_date)->format("d.m.Y.") : null,
			'text_release_date' => $this->text_release_date,
			'text_release_date_formated' => $this->text_release_date ? Carbon::parse($this->text_release_date)->format("d.m.Y.") : null,
			'text_start_date' => $this->text_start_date,
			'text_start_date_formated' => $this->text_start_date ? Carbon::parse($this->text_start_date)->format("d.m.Y.") : null,
			'int_start_date' => $this->int_start_date,
			'int_start_date_formated' => $this->int_start_date ? Carbon::parse($this->int_start_date)->format("d.m.Y.") : null,
			'user_id' => $this->user_id,
			'legal_basis' => $this->legal_basis,
			'basis' => $this->basis,
			'invalid_basis' => $this->invalid_basis,
			'invalid_regulation' => $this->invalid_regulation,
			'legal_predecessor_end_date' => $this->legal_predecessor_end_date,
			'legal_predecessor_end_date_formated' => $this->legal_predecessor_end_date ? Carbon::parse($this->legal_predecessor_end_date)->format("d.m.Y.") : null,
			'historical_version' => $this->historical_version,
			'note' => $this->note,
			'messenger_note' => $this->messenger_note,
			'download_file' => FileResource::make($this->whenLoaded('downloadFile')),
			'pdf_file' => FileResource::make($this->whenLoaded('pdfFile')),
			'html_files' => FileResource::collection($this->whenLoaded('htmlFiles')),
			'html_file' => FileResource::make($this->whenLoaded('htmlFile')),
			'html_file_content' => $this->whenLoaded('htmlFile') ? file_get_contents(storage_path().'/app/'.$this->htmlFile->file_path) : null,
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

	/**
	 * Get html string from word doc
	 *
	 * @param string $filePath
	 * @return string
	 */
	/* public function getHtml($filePath) : ?string
    {
		if($this->isWordDoc($filePath)){
			$phpWord = \PhpOffice\PhpWord\IOFactory::load(storage_path().'/app/'.$filePath);
			$htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
	
			$content = '';
	
			$content .= '<div>' . PHP_EOL;
			$content .= $htmlWriter->getWriterPart('Head')->write();
			$content .= $htmlWriter->getWriterPart('Body')->write();
			$content .= '</div>' . PHP_EOL;
	
			return $content;
		} else if($this->isHtmlDoc($filePath)){
			return file_get_contents(storage_path().'/app/'.$filePath);
		}
		return "";
    } */

	public function isWordDoc($path)
	{
		$formats = [
			'docx',
			'dotx',
			//'doc',
			//'dot',
			//'odt',
			//'docm',
			//'rtf',
			//'uot',
			//'xml',
			'html',
			//'txt',
		];
		if(!$path) return false;
		$extension = pathinfo(storage_path().'/app/'.$path, PATHINFO_EXTENSION);
		if(in_array($extension, $formats)) {
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
