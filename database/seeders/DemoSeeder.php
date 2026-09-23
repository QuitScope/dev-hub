<?php

namespace Database\Seeders;

use App\Models\User;
use Domain\Enums\BugPriority;
use Domain\Enums\BugStatus;
use Domain\Enums\JiraIssuePriority;
use Domain\Enums\JiraIssueStatus;
use Domain\Enums\JiraIssueType;
use Domain\Models\Bug;
use Domain\Models\JiraIssue;
use Domain\Models\JiraSetting;
use Domain\Models\Snippet;
use Domain\Models\Todo;
use Illuminate\Database\Seeder;

/**
 * Realistic sample data for demos and screenshots.
 *
 * php artisan db:seed --class=DemoSeeder
 * Login: demo@devhub.test / password
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'demo@devhub.test'],
            ['name' => 'Demo User', 'password' => bcrypt('password')],
        );

        JiraSetting::query()->updateOrCreate(['user_id' => $user->id], [
            'jira_url' => 'https://example.atlassian.net',
            'jira_email' => $user->email,
            'jira_api_token' => 'demo-token',
            'sync_enabled' => false,
            'last_sync_at' => now()->subHour(),
        ]);

        $issues = [
            ['DEV-101', 'Pagination für Bug-Liste vereinheitlichen', JiraIssueStatus::DONE, JiraIssuePriority::MEDIUM, JiraIssueType::IMPROVEMENT],
            ['DEV-114', 'Jira-Sync als Scheduled Command', JiraIssueStatus::IN_PROGRESS, JiraIssuePriority::HIGH, JiraIssueType::STORY],
            ['DEV-117', 'Analytics-Forecast für Bug-Trends', JiraIssueStatus::REVIEW, JiraIssuePriority::MEDIUM, JiraIssueType::NEW_FEATURE],
            ['DEV-121', 'Login schlägt bei abgelaufenem Token still fehl', JiraIssueStatus::TODO, JiraIssuePriority::HIGHEST, JiraIssueType::BUG],
            ['DEV-125', 'Snippet-Tags filterbar machen', JiraIssueStatus::TESTING, JiraIssuePriority::LOW, JiraIssueType::TASK],
        ];

        foreach ($issues as $i => [$key, $summary, $status, $priority, $type]) {
            JiraIssue::query()->updateOrCreate(['jira_key' => $key], [
                'jira_id' => (string) (10100 + $i),
                'summary' => $summary,
                'status' => $status,
                'priority' => $priority,
                'issue_type' => $type,
                'assignee' => 'Demo User',
                'jira_created_at' => now()->subDays(20 - $i * 3),
                'jira_updated_at' => now()->subDays($i),
                'url' => "https://example.atlassian.net/browse/{$key}",
            ]);
        }

        $snippets = [
            ['Eloquent: Eager Loading mit Constraints', 'N+1-Queries vermeiden und nur benötigte Relationen laden.', 'php', ['laravel', 'eloquent', 'performance'],
                "Post::query()\n    ->with(['comments' => fn (\$query) => \$query->latest()->limit(5)])\n    ->withCount('likes')\n    ->paginate(20);"],
            ['Spatie Query Builder: eigener Filter', 'Volltextsuche über mehrere Spalten als AllowedFilter.', 'php', ['laravel', 'api', 'filter'],
                "AllowedFilter::callback('search', function (Builder \$query, string \$value) {\n    \$query->where(fn (\$q) => \$q\n        ->where('title', 'like', \"%{\$value}%\")\n        ->orWhere('code', 'like', \"%{\$value}%\"));\n});"],
            ['React: useDebounce Hook', 'Eingaben verzögert weitergeben, z. B. für Suchfelder.', 'typescript', ['react', 'hooks'],
                "export function useDebounce<T>(value: T, delay = 300): T {\n  const [debounced, setDebounced] = useState(value)\n  useEffect(() => {\n    const id = setTimeout(() => setDebounced(value), delay)\n    return () => clearTimeout(id)\n  }, [value, delay])\n  return debounced\n}"],
            ['Docker: PHP-FPM Healthcheck', 'Container erst als healthy markieren, wenn FPM antwortet.', 'dockerfile', ['docker', 'devops'],
                "HEALTHCHECK --interval=30s --timeout=3s \\\n  CMD cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1"],
            ['SQL: Doppelte Einträge finden', 'Duplikate nach E-Mail gruppieren.', 'sql', ['sql', 'database'],
                "SELECT email, COUNT(*) AS total\nFROM users\nGROUP BY email\nHAVING COUNT(*) > 1;"],
            ['Git: Branches ohne Remote aufräumen', 'Lokale Branches löschen, deren Remote entfernt wurde.', 'bash', ['git', 'cli'],
                "git fetch -p && git branch -vv | awk '/: gone]/{print \$1}' | xargs -r git branch -D"],
        ];

        foreach ($snippets as [$title, $description, $language, $tags, $code]) {
            Snippet::query()->updateOrCreate(['title' => $title], compact('description', 'language', 'tags', 'code'));
        }

        $todos = [
            ['API-Dokumentation mit Beispielen ergänzen', 'Work', 'High', 'In Progress', 'DEV-114', 3],
            ['Rate Limiting für Auth-Routen einbauen', 'Work', 'High', 'Todo', 'DEV-121', 5],
            ['Feature-Tests für Jira-Notes schreiben', 'Work', 'Medium', 'Todo', null, 7],
            ['Analytics-Snapshots per Cron prüfen', 'Work', 'Medium', 'Done', 'DEV-117', -2],
            ['Laravel Queues vertiefen', 'Learning', 'Low', 'In Progress', null, 14],
            ['Talk über DDD in Laravel ansehen', 'Learning', 'Low', 'Todo', null, null],
            ['Portfolio-Screenshots aktualisieren', 'Private', 'Medium', 'Done', null, -1],
        ];

        foreach ($todos as [$title, $category, $priority, $status, $jira, $dueInDays]) {
            Todo::query()->updateOrCreate(['title' => $title], [
                'category' => $category,
                'priority' => $priority,
                'status' => $status,
                'jira_issue' => $jira,
                'due_date' => $dueInDays === null ? null : now()->addDays($dueInDays),
            ]);
        }

        $bugs = [
            ['Token-Refresh liefert 500 statt 401', BugPriority::Critical, BugStatus::InProgress, 'QA Team', 'Demo User', 'DEV-121', ['auth', 'api']],
            ['Sortierung nach Priorität ignoriert "critical"', BugPriority::High, BugStatus::Reported, 'Support', null, null, ['query', 'sorting']],
            ['Jira-Sync bricht bei leerem Assignee ab', BugPriority::High, BugStatus::Resolved, 'Demo User', 'Demo User', 'DEV-114', ['jira', 'sync']],
            ['Datumsformat im Export uneinheitlich', BugPriority::Medium, BugStatus::Reported, 'Product Owner', null, null, ['export']],
            ['Snippet-Suche findet keine Tags', BugPriority::Medium, BugStatus::InProgress, 'QA Team', 'Demo User', 'DEV-125', ['search']],
            ['Dark Mode: Kontrast der Badges zu gering', BugPriority::Low, BugStatus::Closed, 'Design', 'Demo User', null, ['ui']],
        ];

        foreach ($bugs as $i => [$title, $priority, $status, $reporter, $assignee, $jira, $tags]) {
            $reported = now()->subDays(12 - $i);

            Bug::query()->updateOrCreate(['title' => $title], [
                'priority' => $priority,
                'status' => $status,
                'reporter' => $reporter,
                'assignee' => $assignee,
                'jira_issue' => $jira,
                'tags' => $tags,
                'reported_date' => $reported,
                'processed_date' => $status === BugStatus::Reported ? null : $reported->copy()->addDay(),
                'resolved_date' => in_array($status, [BugStatus::Resolved, BugStatus::Closed]) ? $reported->copy()->addDays(3) : null,
            ]);
        }
    }
}
