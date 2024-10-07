<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostPromptRequest;
use App\Repositories\PromptRepository;
use App\Services\OpenAiService;
use App\Services\PromptService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    /**
     * Gets last 2 prompts by current user
     * 
     * @OA\Get(
     *    path="/prompts",
     *    tags={"prompts"},
     *    summary="Get user last 2 prompts",
     *    description="Get user last 2 prompts",
     *    operationId="getPrompts",
     *    @OA\Response(response=200, description="successful operation"),
     *    @OA\Response(response=400, description="An error happened"),
     *  )
     * @return JsonResponse
     */
    public function getPrompts(
        Request $request,
        PromptRepository $promptRepository
    ): JsonResponse
    {
        try {
            $prompts =  $promptRepository->getLast2PromptsByUser($request->user()->id);

            return new JsonResponse(['prompts' => $prompts]);
        } catch (Exception $ex) {
            return new JsonResponse([
                'error_code' => $ex->getCode(),
                'error_message' => $ex->getMessage(),
            ], 400);
        }
    }

    /**
     * Gets last 2 prompts by current user
     * 
     * @OA\Post(
     *    path="/prompt",
     *    tags={"prompt"},
     *    summary="Send Prompt to Open Api",
     *    description="Send Prompt to Open Api",
     *    operationId="postPrompt",
     *    @OA\Response(response=200, description="successful operation"),
     *    @OA\Response(response=400, description="An error happened"),
     *    @OA\Response(response=422, description="Invalid Prompt information"),
     *  )
     * @return JsonResponse
     */
    public function postPrompt(PostPromptRequest $postPromptRequest, OpenAiService $openAiService): JsonResponse
    {
        try {
            $promptAnswer = $openAiService->makeRequest($postPromptRequest->get('prompt'));

            $prompt = (new PromptService)->savePrompt(
                promptId: null,
                prompt: $postPromptRequest->get('prompt'),
                promptAnswer: $promptAnswer,
                userId: $postPromptRequest->user()->id,
            );

            return new JsonResponse($prompt);
        } catch (Exception $ex) {
            return new JsonResponse([
                'error_code' => $ex->getCode(),
                'error_message' => $ex->getMessage(),
            ], 400);
        }
    }
    
}
