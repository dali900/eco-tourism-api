<?php

namespace App\Http\Controllers;

use App\Http\Requests\Language\LanguageCreateRequest;
use App\Http\Requests\Language\LanguageUpdateRequest;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $languages = null;
        if (!$user->hasEditorAccess()){
            $languages = Language::where('visible', 1)->get();
        } else {
            $languages = Language::get();
        }
        return $this->responseSuccess($languages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageCreateRequest $request)
    {
        $user = auth()->user();

        $data = $request->all();
        $data['created_by'] = $user->id;
        $language = Language::create($data);

        return $this->responseSuccess($language);
    }

    /**
     * Display the specified resource.
     */
    public function get(string $id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageUpdateRequest $request, string $id)
    { 
        $user = auth()->user();

        $language = Language::find($id);
        if(!$language){
            return $this->responseNotFound();
        }

        $data = $request->all();
        $data['updated_by'] = $user->id;
        $language->update($data);

        return $this->responseSuccess($language);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $language = Language::find($id);
        if(!$language){
            return $this->responseNotFound();
        }
        $language->delete();

        return $this->responseSuccess();
    }
}
