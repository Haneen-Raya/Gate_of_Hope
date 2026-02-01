<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Programs\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Modules\Programs\Services\ProgramService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Programs\Http\Requests\V1\Program\StoreProgramRequest;
use Modules\Programs\Http\Requests\V1\Program\UpdateProgramRequest;

/**
 * @class ProgramController
 * * API Controller for managing Hope Gate Programs.
 * Utilizes ProgramService for business logic and ProgramPolicy for security.
 */
class ProgramController extends Controller
{
    use AuthorizesRequests ;
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,Modules\Programs\Models\Program', only: ['index']),
            new Middleware('can:view,program', only: ['show']),
            new Middleware('can:create,Modules\Programs\Models\Program', only: ['store']),
            new Middleware('can:update,program', only: ['update']),
            new Middleware('can:delete,program', only: ['destroy']),
        ];
    }
    public function __construct(protected ProgramService $programService)
    {
        // Automatically maps controller methods to Policy methods
        // $this->authorizeResource(Program::class, 'program');
    }

    /**
     * Display a paginated list of programs.
     * @path GET /api/v1/programs
     */
    public function index(Request $request): JsonResponse
    {
        $programs = $this->programService->listPrograms($request->all());
        return response()->json(['status' => 'success', 'data' => $programs]);
    }

    /**
     * Store a newly created program in storage.
     * @path POST /api/v1/programs
     */
    public function store(StoreProgramRequest $request): JsonResponse
    {
        $program = $this->programService->createProgram($request->validated());
        return response()->json(['status' => 'success', 'message' => 'Program created', 'data' => $program], 201);
    }

    /**
     * Display the specified program.
     * @path GET /api/v1/programs/{id}
     */
    public function show($id): JsonResponse
    {
        $program = $this->programService->getProgramById((int)$id);
        return response()->json(['status' => 'success', 'data' => $program]);
    }

    /**
     * Update the specified program in storage.
     * @path PUT /api/v1/programs/{program}
     */
    public function update(UpdateProgramRequest $request, $id): JsonResponse
    {
        $program = $this->programService->updateProgram((int)$id, $request->validated());
        return response()->json(['status' => 'success', 'message' => 'Program updated', 'data' => $program]);
    }

    /**
     * Remove the specified program from storage.
     * @path DELETE /api/v1/programs/{program}
     */
    public function destroy($id): JsonResponse
    {
        $this->programService->deleteProgram((int)$id);
        return response()->json(['status' => 'success', 'message' => 'Program deleted successfully']);
    }
}
