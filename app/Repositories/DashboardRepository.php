<?php
namespace App\Repositories;

use App\Contracts\DashboardRepositoryInterface;
use App\Models\Article;
use Illuminate\Support\Facades\DB;

class DashboardRepository extends ModelRepository implements DashboardRepositoryInterface 
{
    function countRows($app): array
    {
        $dashboardQuery = DB::select(
            "
                SELECT
                (SELECT COUNT(*) FROM users WHERE app LIKE ?) AS users_row_count,
                (SELECT COUNT(*) FROM subscriptions WHERE app = ?) AS subscriptions_row_count,
                (SELECT COUNT(*) FROM free_trials WHERE app = ?) AS free_trials_row_count,
                (SELECT COUNT(*) FROM news WHERE app = ?) AS news_row_count,
                (SELECT COUNT(*) FROM questions WHERE app = ?) AS questions_row_count,
                (SELECT COUNT(*) FROM regulations WHERE app = ?) AS regulations_row_count,
                (SELECT COUNT(*) FROM documents WHERE app = ?) AS documents_row_count,
                (SELECT COUNT(*) FROM articles WHERE app = ?) AS articles_row_count;
            ", 
            ["%$app%", $app, $app, $app, $app, $app, $app, $app]
        );
        return $dashboardQuery;
    }
}
