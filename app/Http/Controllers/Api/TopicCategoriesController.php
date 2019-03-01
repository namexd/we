<?php

namespace App\Http\Controllers\Api;

use App\Models\TopicCategory;
use App\Transformers\TopicCategoryTransformer;
use Illuminate\Http\Request;

class TopicCategoriesController extends Controller
{

    public function index(Request $request)
    {
        $categories = TopicCategory::where('status', 1)->get();
        return $this->response->collection($categories, new TopicCategoryTransformer());
    }

    public function show(TopicCategory $category)
    {
        return $this->response->item($category, new TopicCategoryTransformer());
    }

}
