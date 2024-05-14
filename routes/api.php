<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ArticleTypeController;
use App\Http\Controllers\AttractionCategoryController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegulationController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BannersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsCategoryController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\RegulationTypeController;
use App\Http\Controllers\Plan\FreeTrialController;
use App\Http\Controllers\Plan\SubscriptionController;
use App\Http\Controllers\Plan\FreeTrialPlanController;
use App\Http\Controllers\Plan\SubscriptionPlanController;
use App\Http\Controllers\ReportErrorController;
use App\Http\Controllers\TripController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Report error
Route::post('/report-error', [ReportErrorController::class, 'reportError']);

Route::get('/', [HomeController::class, 'index']);
Route::get('/menu', [HomeController::class, 'getMenu']);

Route::middleware('auth:sanctum')->get('/me', [UserController::class, 'getAuthUser']);
Route::get('/home-page-data', [HomeController::class, 'getHomePageData']);

//Login
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::prefix('/users')->group(function () {
    Route::post('/', [UserController::class, 'create']);
});

//News
Route::prefix('/news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/{id}', [NewsController::class, 'get']);
});

//Trips
Route::prefix('/trips')->group(function () {
    Route::get('/', [TripController::class, 'index']);
    Route::get('/{id}', [TripController::class, 'get']);
});

//News categories
Route::prefix('/news-categories')->group(function () {
    Route::get('/', [NewsCategoryController::class, 'index']);
    Route::get('/category/{id}', [NewsCategoryController::class, 'getCategoryNews']);
    Route::get('/roots', [NewsCategoryController::class, 'getRoots']);
    Route::get('/tree', [NewsCategoryController::class, 'getTree']);
    Route::get('/{id}', [NewsCategoryController::class, 'get']);
});

//Articles for everyone
Route::prefix('/articles')->group(function () {
    Route::get('/', [ArticleController::class, 'getAll']);
    Route::get('/{id}', [ArticleController::class, 'get']);
});

//Article types types for everyone
Route::prefix('/article-types')->group(function () {
    Route::get('/', [ArticleTypeController::class, 'getAll']);
    Route::get('/roots', [ArticleTypeController::class, 'getRoots']);
    Route::get('/{id}', [ArticleTypeController::class, 'get']);
});

//Attracations
Route::prefix('/attractions')->group(function () {
    Route::get('/', [AttractionController::class, 'index']);
    Route::get('/{id}', [AttractionController::class, 'get']);
    Route::post('/download-file/{id}', [AttractionController::class, 'downloadFile']);
});

//Attracation categories
Route::prefix('/attraction-categories')->group(function () {
    Route::get('/', [AttractionCategoryController::class, 'index']);
    Route::get('/category/{id}', [AttractionCategoryController::class, 'getCatagoryAttractions']);
    Route::get('/roots', [AttractionCategoryController::class, 'getRoots']);
    Route::get('/tree', [AttractionCategoryController::class, 'getTree']);
    Route::get('/{id}', [AttractionCategoryController::class, 'get']);
});

//Place
Route::prefix('/places')->group(function () {
    Route::get('/', [PlaceController::class, 'index']);
    Route::get('/{id}', [PlaceController::class, 'get']);
});

//Banners for everyone
Route::prefix('/banners')->group(function () {
    Route::get('/', [BannerController::class, 'getAll']);
    Route::get('/left-banner', [BannerController::class, 'getLeft']);
    Route::get('/paginated', [BannerController::class, 'getPaginated']);
    Route::get('/slug/{slug}', [BannerController::class, 'getBySlug']);
    Route::get('/{id}', [BannerController::class, 'get']);
});


//Auth
Route::middleware('auth:sanctum')->group(function () {
    //Logout
    Route::get('/logout', [LoginController::class, 'logout']);
    
    //Admin
    //Dashboard
    Route::prefix('/dashboard')->middleware('role:admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
    });
    //Files
    Route::prefix('files')->middleware('role:admin')->group(function () {
        Route::post('/upload', [FilesController::class, 'upload']);
        Route::post('/upload-multiple', [FilesController::class, 'uploadMultiple']);
        Route::post('/delete', [FilesController::class, 'delete']);
        Route::post('/delete-tmp-file', [FilesController::class, 'deleteTmpFile']);
    });
    //Attractions
    Route::prefix('/attractions')->middleware('role:author')->group(function () {
        Route::post('/', [AttractionController::class, 'store']);
        Route::put('/{id}', [AttractionController::class, 'update']);
        Route::delete('/{id}', [AttractionController::class, 'destroy']);
        Route::delete('/file/{id}', [AttractionController::class, 'deleteFile']);
    });
    //Attracation categories
    Route::prefix('/attraction-categories')->middleware('role:author')->group(function () {
        Route::post('/', [AttractionCategoryController::class, 'store']);
        Route::put('/{id}', [AttractionCategoryController::class, 'update']);
        Route::delete('/{id}', [AttractionCategoryController::class, 'destroy']);
    });
    //Places
    Route::prefix('/places')->middleware('role:author')->group(function () {
        Route::post('/', [PlaceController::class, 'store']);
        Route::put('/{id}', [PlaceController::class, 'update']);
        Route::delete('/{id}', [PlaceController::class, 'destroy']);
    });
    //Users
    Route::prefix('/users')->middleware('role:admin')->group(function () {
        Route::get('/roles', [UserController::class, 'getRoles']);
        Route::get('/', [UserController::class, 'getAll']);
        Route::get('/profiles', [UserController::class, 'getUserProfiles']);
        Route::post('/admin-create', [UserController::class, 'adminCreate']);
        Route::post('/export-excel', [UserController::class, 'exportExcel']);
        Route::get('/{id}/profile', [UserController::class, 'getUserProfile']);
        Route::get('/{id}', [UserController::class, 'getUser']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::put('/{id}/password', [UserController::class, 'updatePassword']);
        Route::delete('/{id}', [UserController::class, 'delete']);
    });
    //News
    Route::prefix('/news')->middleware('role:author')->group(function () {
        Route::post('/', [NewsController::class, 'store']);
        Route::put('/{id}', [NewsController::class, 'update']);
        Route::delete('/{id}', [NewsController::class, 'delete']);
        Route::delete('/file/{id}', [NewsController::class, 'deleteFile']);
    });
    //News categories
    Route::prefix('/news-categories')->middleware('role:author')->group(function () {
        Route::post('/', [NewsCategoryController::class, 'store']);
        Route::put('/{id}', [NewsCategoryController::class, 'update']);
        Route::delete('/{id}', [NewsCategoryController::class, 'destroy']);
    });
    //Trips
    Route::prefix('/trips')->middleware('role:author')->group(function () {
        Route::post('/', [TripController::class, 'store']);
        Route::put('/{id}', [TripController::class, 'update']);
        Route::delete('/{id}', [TripController::class, 'destroy']);
    });
    //Article
    Route::prefix('/articles')->middleware('role:author')->group(function () {
        Route::post('/', [ArticleController::class, 'create']);
        Route::put('/{id}', [ArticleController::class, 'update']);
        Route::delete('/{id}', [ArticleController::class, 'delete']);
        Route::delete('/file/{id}', [ArticleController::class, 'deleteFile']);
        Route::delete('/preview-file/{id}', [ArticleController::class, 'deletePreviewFile']);
    });
    //Article types
    Route::prefix('/article-types')->middleware('role:author')->group(function () {
        Route::post('/', [ArticleTypeController::class, 'create']);
        Route::put('/{id}', [ArticleTypeController::class, 'update']);
        Route::delete('/{id}', [ArticleTypeController::class, 'delete']);
    });
    //Banners
    Route::prefix('/banners')->middleware('role:author')->group(function () {
        Route::post('/', [BannerController::class, 'create']);
        Route::put('/{id}', [BannerController::class, 'update']);
        Route::delete('/{id}', [BannerController::class, 'delete']);
    });
    //Account
    //Free trial plan
    Route::prefix('free-trial-plans')->middleware('role:admin')->group(function () {
        Route::get('/', [FreeTrialPlanController::class, 'index']);
        Route::get('/{id}', [FreeTrialPlanController::class, 'get']);
        Route::post('/', [FreeTrialPlanController::class, 'create']);
        Route::put('/{id}', [FreeTrialPlanController::class, 'update']);
        Route::delete('/{id}', [FreeTrialPlanController::class, 'delete']);
    });
    //Free trial
    Route::prefix('/free-trials')->middleware('role:admin')->group(function () {
        Route::get('/', [FreeTrialController::class, 'index']);
        Route::get('/user/{userId}', [FreeTrialController::class, 'getUserFreeTrial']);
        Route::get('/{id}', [FreeTrialController::class, 'get']);
        Route::post('/', [FreeTrialController::class, 'create']);
        Route::put('/{id}', [FreeTrialController::class, 'update']);
    });
    Route::prefix('free-trials')->middleware('role:admin')->group(function () {
        Route::delete('/{id}', [FreeTrialController::class, 'delete']);
    });
    //Subscription plan
    Route::prefix('subscription-plans')->middleware('role:admin')->group(function () {
        Route::get('/', [SubscriptionPlanController::class, 'index']);
        Route::get('/{id}', [SubscriptionPlanController::class, 'get']);
        Route::post('/', [SubscriptionPlanController::class, 'create']);
        Route::put('/{id}', [SubscriptionPlanController::class, 'update']);
        Route::delete('/{id}', [SubscriptionPlanController::class, 'delete']);
    });
    //Subscription
    Route::prefix('/subscriptions')->middleware('role:admin')->group(function () {
        Route::get('/user/{userId}', [SubscriptionController::class, 'getUserSubscriptions']);
        Route::post('/', [SubscriptionController::class, 'create']);
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::get('/{id}', [SubscriptionController::class, 'get']);
        Route::put('/{id}', [SubscriptionController::class, 'update']);
        Route::delete('/{id}', [SubscriptionController::class, 'delete']);
    });
    Route::prefix('subscriptions')->middleware('role:admin')->group(function () {
        Route::delete('/{id}', [SubscriptionController::class, 'delete']);
    });
});
