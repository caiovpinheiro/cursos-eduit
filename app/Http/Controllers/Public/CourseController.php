<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Http\Resources\PublicCourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    /**
     * Display a listing of public courses
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $category = $request->get('category');
        $difficulty = $request->get('difficulty');
        $isFree = $request->get('is_free');
        $search = $request->get('search');

        $query = Course::where('is_active', true);

        // Filtros
        if ($category) {
            $query->where('category', $category);
        }

        if ($difficulty) {
            $query->where('difficulty_level', $difficulty);
        }

        if ($isFree !== null) {
            $query->where('price', $isFree ? '=' : '>', 0);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate($perPage);

        return response()->json([
            'courses' => PublicCourseResource::collection($courses->items()),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
                'from' => $courses->firstItem(),
                'to' => $courses->lastItem(),
            ],
            'filters' => [
                'categories' => Course::getCategoryOptions(),
                'difficulty_levels' => Course::getDifficultyLevelOptions(),
            ]
        ]);
    }

    /**
     * Display the specified public course
     */
    public function show($id): JsonResponse
    {
        $course = Course::where('is_active', true)
            ->with(['activities' => function ($query) {
                $query->select('id', 'course_id', 'title', 'order', 'activityable_type', 'activityable_id')
                    ->with('activityable')
                    ->orderBy('order');
            }])
            ->find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        return response()->json([
            'course' => new PublicCourseResource($course)
        ]);
    }

    /**
     * Get course categories
     */
    public function categories(): JsonResponse
    {
        return response()->json([
            'categories' => Course::getCategoryOptions()
        ]);
    }

    /**
     * Get difficulty levels
     */
    public function difficultyLevels(): JsonResponse
    {
        return response()->json([
            'difficulty_levels' => Course::getDifficultyLevelOptions()
        ]);
    }
}
