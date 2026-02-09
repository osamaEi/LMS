<?php

namespace App\Services\Xapi;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Str;

class XapiStatementBuilder
{
    // Verb IRI mappings (xAPI standard verbs)
    private array $verbMap = [
        'login'              => 'https://w3id.org/xapi/adl/verbs/logged-in',
        'logout'             => 'https://w3id.org/xapi/adl/verbs/logged-out',
        'register'           => 'http://adlnet.gov/expapi/verbs/registered',
        'view_session'       => 'http://id.tincanapi.com/verb/viewed',
        'view_recording'     => 'http://id.tincanapi.com/verb/viewed',
        'view_subject'       => 'http://id.tincanapi.com/verb/viewed',
        'start_quiz'         => 'http://adlnet.gov/expapi/verbs/attempted',
        'submit_quiz'        => 'http://adlnet.gov/expapi/verbs/completed',
        'submit_assignment'  => 'http://adlnet.gov/expapi/verbs/completed',
        'join_session'       => 'http://adlnet.gov/expapi/verbs/attended',
        'leave_session'      => 'https://w3id.org/xapi/adl/verbs/abandoned',
        'enroll'             => 'http://adlnet.gov/expapi/verbs/registered',
        'grade_evaluation'   => 'http://adlnet.gov/expapi/verbs/scored',
        'submit_survey'      => 'http://adlnet.gov/expapi/verbs/responded',
        'submit_rating'      => 'http://id.tincanapi.com/verb/rated',
        'download_file'      => 'http://id.tincanapi.com/verb/downloaded',
        'create_ticket'      => 'http://activitystrea.ms/schema/1.0/create',
    ];

    /**
     * Build xAPI statement from activity log
     */
    public function build(ActivityLog $log): array
    {
        $statement = [
            'id' => (string) Str::uuid(),
            'timestamp' => $log->created_at->toIso8601String(),
            'actor' => $this->buildActor($log->user),
            'verb' => $this->buildVerb($log->action),
            'object' => $this->buildObject($log),
            'context' => $this->buildContext($log),
        ];

        // Add result if applicable
        $result = $this->buildResult($log);
        if ($result) {
            $statement['result'] = $result;
        }

        return $statement;
    }

    /**
     * Build actor (the user)
     */
    private function buildActor(?User $user): array
    {
        if (!$user) {
            return [
                'objectType' => 'Agent',
                'name' => 'Anonymous',
            ];
        }

        $actor = [
            'objectType' => 'Agent',
            'name' => $user->name,
            'mbox' => 'mailto:' . $user->email,
        ];

        // Add account if national_id exists (NELC identifier)
        if ($user->national_id) {
            $actor['account'] = [
                'homePage' => config('xapi.platform_iri'),
                'name' => $user->national_id,
            ];
        }

        return $actor;
    }

    /**
     * Build verb (the action)
     */
    private function buildVerb(string $action): array
    {
        $verbId = $this->verbMap[$action] ?? 'http://activitystrea.ms/schema/1.0/' . str_replace('_', '-', $action);

        return [
            'id' => $verbId,
            'display' => [
                'en-US' => ucwords(str_replace('_', ' ', $action)),
                'ar-SA' => ActivityLog::where('action', $action)->first()?->getDescription() ?? $action,
            ],
        ];
    }

    /**
     * Build object (what the action was performed on)
     */
    private function buildObject(ActivityLog $log): array
    {
        $platformIri = config('xapi.platform_iri');

        if ($log->loggable) {
            $objectType = class_basename($log->loggable_type);
            $objectId = $log->loggable_id;

            $objectIri = "{$platformIri}/activities/{$objectType}/{$objectId}";

            $object = [
                'objectType' => 'Activity',
                'id' => $objectIri,
                'definition' => [
                    'type' => 'http://activitystrea.ms/schema/1.0/activity',
                    'name' => [
                        'en-US' => "{$objectType} {$objectId}",
                    ],
                ],
            ];

            // Add more context based on loggable type
            if (method_exists($log->loggable, 'name')) {
                $object['definition']['name']['en-US'] = $log->loggable->name ?? "{$objectType} {$objectId}";
            }

            return $object;
        }

        // Generic activity for actions without specific objects
        return [
            'objectType' => 'Activity',
            'id' => "{$platformIri}/activities/{$log->action}",
            'definition' => [
                'type' => 'http://activitystrea.ms/schema/1.0/activity',
                'name' => [
                    'en-US' => ucwords(str_replace('_', ' ', $log->action)),
                    'ar-SA' => $log->getDescription(),
                ],
            ],
        ];
    }

    /**
     * Build result (score, completion, etc.)
     */
    private function buildResult(ActivityLog $log): ?array
    {
        $properties = $log->properties ?? [];

        if (empty($properties)) {
            return null;
        }

        $result = [];

        // Score (for graded activities)
        if (isset($properties['score'])) {
            $result['score'] = [
                'raw' => (float) $properties['score'],
            ];

            if (isset($properties['max_score'])) {
                $result['score']['max'] = (float) $properties['max_score'];
                $result['score']['min'] = 0;
                $result['score']['scaled'] = $properties['score'] / $properties['max_score'];
            }
        }

        // Completion
        if (isset($properties['completed'])) {
            $result['completion'] = (bool) $properties['completed'];
        }

        // Success
        if (isset($properties['success'])) {
            $result['success'] = (bool) $properties['success'];
        }

        // Duration (in seconds, convert to ISO 8601 duration)
        if (isset($properties['duration'])) {
            $result['duration'] = 'PT' . (int) $properties['duration'] . 'S';
        }

        return empty($result) ? null : $result;
    }

    /**
     * Build context (platform, instructor, etc.)
     */
    private function buildContext(ActivityLog $log): array
    {
        $context = [
            'platform' => config('xapi.platform_name_en'),
            'language' => app()->getLocale(),
        ];

        // Add session context
        if ($log->session_id) {
            $context['registration'] = $log->session_id;
        }

        // Add context activities (parent/grouping)
        $contextActivities = [];

        if ($log->loggable && $log->loggable_type) {
            $platformIri = config('xapi.platform_iri');

            // For sessions, add subject as parent
            if (str_contains($log->loggable_type, 'Session') && method_exists($log->loggable, 'subject')) {
                $subject = $log->loggable->subject;
                if ($subject) {
                    $contextActivities['parent'] = [[
                        'objectType' => 'Activity',
                        'id' => "{$platformIri}/activities/Subject/{$subject->id}",
                    ]];
                }
            }
        }

        if (!empty($contextActivities)) {
            $context['contextActivities'] = $contextActivities;
        }

        return $context;
    }
}
