<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Http\Resources\TemplateResource;

class TemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        $templates = Template::paginate(10); // Adjust pagination as needed
        return TemplateResource::collection($templates);
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(StoreTemplateRequest $request)
    {
        $template = Template::create($request->validated());

        return new TemplateResource($template);
    }

    /**
     * Display the specified template.
     */
    public function show(Template $template)
    {
        return new TemplateResource($template);
    }

    /**
     * Update the specified template in storage.
     */
    public function update(UpdateTemplateRequest $request, Template $template)
    {
        $template->update($request->validated());

        return new TemplateResource($template);
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(Template $template)
    {
        $template->delete();

        return response()->json(['message' => 'Template deleted successfully']);
    }
}
