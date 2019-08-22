<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Request;
use App\Models\Ucenter\Domain;
use App\Models\Ucenter\LoginConfig;
use App\Models\User;
use App\Transformers\Ucenter\DomainTransformer;
use App\Transformers\Ucenter\UserTransformer;
use function App\Utils\app_access_encode;

class DomainsController extends Controller
{
    protected $model;

    public function __construct(Domain $domain)
    {
        $this->model = $domain;
    }

    public function index()
    {
        $domains = $this->model->paginate(request()->get('pagesize', $this->pagesize));

        return $this->response->paginator($domains, new DomainTransformer())->addMeta('default', LoginConfig::pluck('value', 'slug'));
    }

    public function show($domain)
    {
        $domain = $this->model->where('domain', $domain)->first();
        if ($domain)
        {
            return $this->response->item($domain, new DomainTransformer())
                ->addMeta('default', LoginConfig::pluck('value', 'slug'));
        }else
        {
            return $this->response->array(['meta'=>['default'=> LoginConfig::pluck('value', 'slug')]]);

        }

    }

}
