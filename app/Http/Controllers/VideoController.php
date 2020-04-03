<?php

namespace App\Http\Controllers;

use App\Services\VideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    private VideoService $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * @return JsonResponse
     */
    public function getAll()
    {
        return response()->json(json_decode(
            $this->videoService->getAll()
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchByTag(Request $request)
    {
        return response()->json(json_decode(
            $this->videoService->findByTag($request->tag)
        ));
    }
}
