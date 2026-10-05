<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Course;
use App\Models\VideoActivity;
use App\Models\ArticleActivity;
use App\Models\EmbedContentActivity;
use App\Models\SupportMaterialActivity;
use App\Models\QuizActivity;
use App\Models\FinalExamActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Display activities for a specific course
     */
    public function index(Request $request, $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        
        $activities = Activity::where('course_id', $courseId)
            ->with('activityable')
            ->orderBy('order')
            ->get();

        return response()->json([
            'course' => $course,
            'activities' => $activities->map(function ($activity) {
                return $this->formatActivityResponse($activity);
            }),
        ]);
    }

    /**
     * Store a newly created activity
     */
    public function store(Request $request): JsonResponse
    {
        $type = $request->input('type');
        
        // Validate based on activity type
        $validatedData = $this->validateActivityByType($request, $type);
        
        DB::beginTransaction();
        try {
            // Get next order if not provided
            $order = $request->input('order');
            if (!$order) {
                $order = Activity::where('course_id', $request->course_id)
                    ->max('order') + 1;
            }
            
            // For final exam, ensure it's the last activity
            if ($type === 'final_exam') {
                $order = Activity::where('course_id', $request->course_id)
                    ->max('order') + 1;
            }
            
            // Create the specific activity model
            $activityable = $this->createActivityable($type, $request);
            
            // Create the activity
            $activity = Activity::create([
                'course_id' => $request->course_id,
                'title' => $request->title,
                'order' => $order,
                'activityable_id' => $activityable->id,
                'activityable_type' => get_class($activityable),
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Activity created successfully',
                'activity' => $this->formatActivityResponse($activity->load('activityable')),
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating activity',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified activity
     */
    public function show($id): JsonResponse
    {
        $activity = Activity::with('activityable')->findOrFail($id);
        
        return response()->json([
            'activity' => $this->formatActivityResponse($activity),
        ]);
    }

    /**
     * Update the specified activity
     */
    public function update(Request $request, $id): JsonResponse
    {
        $activity = Activity::with('activityable')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Update activity title and order
            if ($request->has('title')) {
                $activity->title = $request->title;
            }
            
            if ($request->has('order')) {
                $newOrder = (int) $request->order;
                $oldOrder = $activity->order;
                
                // If it's a final exam, prevent changing order unless it's still the last
                if ($activity->activityable_type === FinalExamActivity::class) {
                    $maxOrder = Activity::where('course_id', $activity->course_id)
                        ->where('id', '!=', $activity->id)
                        ->max('order');
                    
                    if ($newOrder <= $maxOrder) {
                        return response()->json([
                            'message' => 'A Prova Final deve sempre ser a última atividade do curso.',
                        ], 422);
                    }
                }
                
                // Shift other activities if order changed
                if ($newOrder !== $oldOrder) {
                    if ($newOrder < $oldOrder) {
                        // Moving up: increment order of activities between new and old position
                        Activity::where('course_id', $activity->course_id)
                            ->where('id', '!=', $activity->id)
                            ->where('order', '>=', $newOrder)
                            ->where('order', '<', $oldOrder)
                            ->increment('order');
                    } else {
                        // Moving down: decrement order of activities between old and new position
                        Activity::where('course_id', $activity->course_id)
                            ->where('id', '!=', $activity->id)
                            ->where('order', '>', $oldOrder)
                            ->where('order', '<=', $newOrder)
                            ->decrement('order');
                    }
                }
                
                $activity->order = $newOrder;
            }
            
            $activity->save();
            
            // Update the activityable data
            $type = $this->getTypeFromClass($activity->activityable_type);
            $activityableData = $request->input($type);
            
            if ($activityableData) {
                $activity->activityable->update($activityableData);
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Activity updated successfully',
                'activity' => $this->formatActivityResponse($activity->fresh('activityable')),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating activity',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update activity order (reordering)
     */
    public function reorder(Request $request, $courseId): JsonResponse
    {
        $request->validate([
            'activities' => 'required|array',
            'activities.*.id' => 'required|exists:activities,id',
            'activities.*.order' => 'required|integer|min:1',
        ]);
        
        DB::beginTransaction();
        try {
            foreach ($request->activities as $activityData) {
                $activity = Activity::findOrFail($activityData['id']);
                
                // Ensure activity belongs to this course
                if ($activity->course_id != $courseId) {
                    throw new \Exception('Activity does not belong to this course');
                }
                
                $activity->order = $activityData['order'];
                $activity->save();
            }
            
            // Ensure final exam is still last
            $finalExam = Activity::where('course_id', $courseId)
                ->whereHasMorph('activityable', [FinalExamActivity::class])
                ->first();
            
            if ($finalExam) {
                $maxOrder = Activity::where('course_id', $courseId)
                    ->where('id', '!=', $finalExam->id)
                    ->max('order');
                
                if ($finalExam->order <= $maxOrder) {
                    $finalExam->order = $maxOrder + 1;
                    $finalExam->save();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Activities reordered successfully',
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error reordering activities',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified activity
     */
    public function destroy($id): JsonResponse
    {
        $activity = Activity::with('activityable')->findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Delete the activityable first
            $activity->activityable->delete();
            
            // Delete the activity
            $activity->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Activity deleted successfully',
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting activity',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate activity data based on type
     */
    private function validateActivityByType(Request $request, string $type): array
    {
        $rules = [
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:video,article,embed_content,support_material,quiz,final_exam',
            'title' => 'required|string|max:255',
            'order' => 'nullable|integer|min:1',
        ];
        
        switch ($type) {
            case 'video':
                $rules = array_merge($rules, [
                    'video.description' => 'nullable|string',
                    'video.transcript' => 'nullable|string',
                    'video.link' => 'required|url|max:500',
                ]);
                break;
                
            case 'article':
                $rules = array_merge($rules, [
                    'article.description' => 'nullable|string',
                    'article.content_richtext' => 'required|string',
                ]);
                break;
                
            case 'embed_content':
                $rules = array_merge($rules, [
                    'embed_content.description' => 'nullable|string',
                    'embed_content.embed_code' => 'required|string',
                ]);
                break;
                
            case 'support_material':
                $rules = array_merge($rules, [
                    'support_material.description' => 'nullable|string',
                    'support_material.content_richtext' => 'required|string',
                ]);
                break;
                
            case 'quiz':
                $rules = array_merge($rules, [
                    'quiz.description' => 'nullable|string',
                    'quiz.duration_minutes' => 'nullable|integer|min:1',
                ]);
                break;
                
            case 'final_exam':
                $rules = array_merge($rules, [
                    'final_exam.description' => 'nullable|string',
                    'final_exam.max_attempts' => 'required|integer|min:1|max:3',
                    'final_exam.duration_minutes' => 'required|integer|min:1',
                ]);
                
                // Check if course already has a final exam
                $existingFinalExam = Activity::where('course_id', $request->course_id)
                    ->whereHasMorph('activityable', [FinalExamActivity::class])
                    ->exists();
                
                if ($existingFinalExam) {
                    return response()->json([
                        'message' => 'Este curso já possui uma Prova Final. Não é possível cadastrar mais de uma por curso.',
                    ], 422)->throwResponse();
                }
                break;
        }
        
        return $request->validate($rules);
    }

    /**
     * Create the activityable model based on type
     */
    private function createActivityable(string $type, Request $request)
    {
        switch ($type) {
            case 'video':
                return VideoActivity::create($request->input('video'));
                
            case 'article':
                return ArticleActivity::create($request->input('article'));
                
            case 'embed_content':
                return EmbedContentActivity::create($request->input('embed_content'));
                
            case 'support_material':
                return SupportMaterialActivity::create($request->input('support_material'));
                
            case 'quiz':
                return QuizActivity::create($request->input('quiz'));
                
            case 'final_exam':
                return FinalExamActivity::create($request->input('final_exam'));
                
            default:
                throw new \Exception('Invalid activity type');
        }
    }

    /**
     * Get type string from class name
     */
    private function getTypeFromClass(string $class): string
    {
        $typeMap = [
            VideoActivity::class => 'video',
            ArticleActivity::class => 'article',
            EmbedContentActivity::class => 'embed_content',
            SupportMaterialActivity::class => 'support_material',
            QuizActivity::class => 'quiz',
            FinalExamActivity::class => 'final_exam',
        ];
        
        return $typeMap[$class] ?? 'unknown';
    }

    /**
     * Format activity response
     */
    private function formatActivityResponse(Activity $activity): array
    {
        $type = $this->getTypeFromClass($activity->activityable_type);
        
        return [
            'id' => $activity->id,
            'course_id' => $activity->course_id,
            'type' => $type,
            'title' => $activity->title,
            'order' => $activity->order,
            $type => $activity->activityable,
            'created_at' => $activity->created_at,
            'updated_at' => $activity->updated_at,
        ];
    }
}
