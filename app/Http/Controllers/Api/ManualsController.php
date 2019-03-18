<?php

namespace App\Http\Controllers\Api;

use App\Models\Manual;
use App\Models\ManualCategory;
use App\Models\ManualPost;
use App\Models\RoleHasManaul;
use App\Models\User;
use App\Transformers\ManualsTransformer;
use Illuminate\Http\Request;

class ManualsController extends Controller
{

    public function index(Request $request,Manual $manual)
    {
        $user=$this->user();
        $manuals=$manual->lists($user,$request->pagesize ?? $this->pagesize);
        return $this->response->paginator($manuals, new ManualsTransformer());
    }

    public function showCategories(Request $request)
    {
         $user=$this->user();
        $slug=$request->slug;
        $categories=(new ManualCategory())->listTree($user,$slug);
        $data['data']=$categories;
        return $this->response->array($data);
    }

    public function showPosts(Request $request)
    {
       $category_id=$request->category_id;
       $data['data']=(new ManualPost)->getDetail($category_id);
       return $this->response->array($data);

    }

}
