<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpParser\Builder\Param;


// Список Дел:
    // test all items coming back from responseWrapper (feature testing)
    // Get tracer code then refine
    // Make tests generic then specific
        // Generic for common use
        // Specific for application

class GlobalAPIController extends Controller
{

    private $endpointKeyToApplyQueryParametersOn;
    public $acceptableParameters = [];
    private $class;
    private $classId;
    private $subRequest;
    private $subRequestId;
    private $endpointKey;
    public $httpMethod;
    public $endpoint;
    private $statusCode;
    private $request;
    
    private $includes = [];
    private $includedMethods = [];
    private $method;
    public $paramsAccepted  = [];
    public $paramsRejected = [];
    private $currentPage;
    private $totalPages;
    private $requestPerPage;
    private $totalResults;
    private $url;

    private $query;
    private $results;
    private $errors = [];
    private $message = [];

    public $acceptedClasses = [
        'caseStudies' => "App\Models\CaseStudy",
        'projects' => "App\Models\Project",
        'content' => "App\Models\Content",
        'experience' => "App\Models\Experience",
        'images' => "App\Models\Image",
        'posts' => "App\Models\Post",
        'resources' => "App\Models\Resource",
        'categories' => "App\Models\Category",
        'tags' => "App\Models\Tag",
        'skillTypes' => "App\Models\SkillType",
        'skills' => "App\Models\Skill",
        'workHistoryTypes' => "App\Models\WorkHistoryType",
        'workHistory' => "App\Models\WorkHistory"
    ];

    public function processRequest($endpointKey, $endpointKeyId = null, $subEndpointKey = null, $subEndpointKeyId = null, $otherInfo = null, Request $request){

        // # validate token or key - done by laravel!
        // Initial set up of key variables
        $this->endpointKeyToApplyQueryParametersOn = $subEndpointKey ?? $endpointKey;
        $this->endpointKey = $endpointKey;
        $this->endpointKeyId = $endpointKeyId;
        $this->subEndpointKey = $subEndpointKey;
        $this->subEndpointKeyId = $subEndpointKeyId;
        // validate has class
        // validate subclass
        
        // validate has permission to access class
        if(!$this->validateRequest()){ // ???
            return $this->responseWrapper();
        }
        
        $this->class = $this->acceptedClasses[$this->endpointKey];
        $this->endpoint = $request->path();
        $this->url = $request->url();
        $this->request = $request;
        $this->path = request()->path;
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? request()->method() ?? null;
        // Getting acceptable parameters
        $tempClass = new $this->class();
        $classTableName = $tempClass->gettable();
        $columnData = $this->arrayOfObjectsToArrayOfArrays(DB::select("SHOW COLUMNS FROM {$classTableName}"));
        $this->setAcceptableParameters($columnData);
        $this->initialProcessOfAllParameters(request()->all(), $this->acceptableParameters);

        if ($this->isGetRequest()){
            $this->getRequest();
        }else{
            $this->postRequest();
        }

        return $this->responseWrapper();
    }

    public function arrayOfObjectsToArrayOfArrays(array $arrayOfObjects)
    {
        foreach ($arrayOfObjects as $object) {
            $arrayOfArrays[] = (array) $object;
        }

        return $arrayOfArrays;
    }

    public function initialProcessOfAllParameters(array $incomingParameters, array $acceptableParameters)
    {
        $defaultAcceptableParameters = ['perPage', 'page', 'orderBy'];

        foreach ($incomingParameters as $key => $value) {
            if (array_key_exists($key, $acceptableParameters) || in_array($key, $defaultAcceptableParameters)) {
                $this->paramsAccepted[$key] = $value;
            } else {
                $this->paramsRejected[$key] = $value;
            } 
        }
        
    }

    public function setAcceptableParameters(array $classDBData)
    {
        foreach ($classDBData as $columnArray) {
            foreach ($columnArray as $key => $value) {
                $this->acceptableParameters[$columnArray['Field']][$key] = $value; 
            }
        }
    }

    private function validateRequest(){
        $isValid = true;

        $this->validateClass() ? null : $isValid = false;
        // $isValid == false || $this->validateHttpMethod() ? null : $isValid = false;

        return $isValid;
    }

    private function validateClass() {
        if(array_key_exists($this->endpointKeyToApplyQueryParametersOn, $this->acceptedClasses)){
            return true;
        }
        $this->results = "'{$this->endpointKey}' is not a valid API path. Please view the documentation at {$this->url}.";
        $this->errors['statusMessage'] = 'Endpoint not found';
        $this->errors['errorMessage'] = "{$this->endpointKey} path not found";
        $this->statusCode = 404;
        return false;
    }

    // private function validateHttpMethod(){
    //     return true;
    // }

    private function isGetRequest(){
        if ($this->httpMethod == 'GET'){
            return true;
        }else{
            return false;
        }
    }

    private function getRequest(){

        if(request('call')){
            $call = request('call');

            if(key_exists($call, $this->class::$api_get['calls'])){
                $this->results[] = $this->class::$call();
                $this->totalResults = count($this->results);
                return;
            }
            $this->results = "'{$this->httpMethod}' is not an accepted call. Please view the documentation at {$this->url}.";
            $this->errors['statusMessage'] = 'Bad Request';
            $this->errors['errorMessage'] = "{$call} not valid";
            return;
        }

        if(request('include')) {
            $relationship = request('include');

            if(!$this->isRelationship($this->class, $relationship)) {
                $this->results = "'{$relationship}' is not in relation to {$this->endpointKey}. Please view the documentation at {$this->url}.";
                $this->errors['statusMessage'] = 'Bad Request';
                $this->errors['errorMessage'] = "{$relationship} not related to {$this->endpointKey}";

                return;
            }

            $model_data = $this->class::all();

            foreach ($model_data as $item) {
                $item->$relationship = $item->$relationship()->get();
                $this->results[] = $item;
            }
            $this->totalResults = count($this->results);
            return;
        }

        $perPage = (int) $perPage = is_numeric(request()->perPage) ? request()->perPage : 15; // @ New
        $this->results = $this->class::paginate($perPage)->appends([
            'perPage' => $perPage // TODO: need to make dynamic, only included it if we use it
        ]); // @ New
        // * $this->results = $this->class::all();
        $this->totalResults = count($this->results);
        return;
    }

    private function isRelationship($class, $relationship) {
        return method_exists($class, $relationship);
    }

    private function postRequest(){
        $this->results = "'{$this->httpMethod}' is not an accepted method. Please view the documentation at {$this->url}.";
        $this->errors['statusMessage'] = 'Bad Request';
        $this->errors['errorMessage'] = "{$this->httpMethod} not valid";
    }

    private function responseWrapper(){

        // * constructing wrapper

        $success = $this->errors ? false : true;
        // 404, 403, 400, 200, 201
        // ? https://restfulapi.net/http-methods/#get
        $statusCode = $this->statusCode ?? ($success ? 200 : 400);
        
        $paramsAccepted = $this->paramsAccepted ?? [];
        $paramsRejected = $this->paramsRejected ?? [];

        $currentPage = $this->currentPage ?? null;
        $totalPages = $this->totalPages ?? null;
        $requestPerPage = $this->requestPerPage ?? null;

        $totalResults = $this->totalResults ?? null;

        $responseData = [
            'success' => $success,
            'statusCode' => $statusCode,
            'errors' => $this->errors,
            'requestMethod' => $this->httpMethod,
            'paramsSent' => [
                'All' => request()->all(),
                'GET' => $_GET,
                'POST' =>  $_POST
            ],
            'paramsAccepted' => $paramsAccepted,
            'paramsRejected' => $paramsRejected,
            'mainEndpoint' => $this->endpointKey,
            'endpoint' => $this->endpoint,
            'endpointUrl' => $this->url,
            'pageInfo' => [
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'requestPerPage' => $requestPerPage
            ],
            'totalResults' => $this->totalResults,
            'results' => $this->results
        ];

        return response()->json($responseData, $statusCode);
    }
}
