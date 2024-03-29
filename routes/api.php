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
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionTypeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RegulationController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BannersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegulationTypeController;
use App\Http\Controllers\Plan\FreeTrialController;
use App\Http\Controllers\Plan\SubscriptionController;
use App\Http\Controllers\Plan\FreeTrialPlanController;
use App\Http\Controllers\Plan\SubscriptionPlanController;
use App\Http\Controllers\ReportErrorController;

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
Route::get('/fake-user', [UserController::class, 'getFakeUser']);
Route::post('/contact', [UserController::class, 'contact']);
Route::post('/contact/banner-1', [BannersController::class, 'banner1Contact']);

//Login
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::prefix('/users')->group(function () {
    Route::post('/', [UserController::class, 'create']);
});

//News for everyone
Route::prefix('/news')->group(function () {
    Route::get('/', [NewsController::class, 'getAll']);
    Route::get('/{id}', [NewsController::class, 'get']);
});

//Documents for everyone
Route::prefix('/documents')->group(function () {
    Route::get('/', [DocumentController::class, 'getAll']);
    Route::get('/{id}', [DocumentController::class, 'get']);
    Route::post('/download-file/{id}', [DocumentController::class, 'downloadFile']);
});

//Document types
Route::prefix('/document-types')->group(function () {
    Route::get('/', [DocumentTypeController::class, 'getAll']);
    Route::get('/roots', [DocumentTypeController::class, 'getRoots']);
    Route::get('/{id}', [DocumentTypeController::class, 'get']);
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

//Questions for everyone
Route::prefix('/questions')->group(function () {
    Route::get('/', [QuestionController::class, 'getAll']);
    Route::get('/{id}', [QuestionController::class, 'get']);
});

//Question types for everyone
Route::prefix('/question-types')->group(function () {
    Route::get('/', [QuestionTypeController::class, 'getAll']);
    Route::get('/roots', [QuestionTypeController::class, 'getRoots']);
    Route::get('/{id}', [QuestionTypeController::class, 'get']);
});

//Videos for everyone
Route::prefix('/videos')->group(function () {
    Route::get('/', [VideoController::class, 'getAll']);
    Route::get('/{id}', [VideoController::class, 'get']);
});

//Regulations
Route::prefix('/regulations')->group(function () {
    Route::get('/', [RegulationController::class, 'getAll']);
    Route::get('/{id}', [RegulationController::class, 'get']);
    Route::post('/download-file/{id}', [RegulationController::class, 'downloadFile']);
});

//Regulation types
Route::prefix('/regulation-types')->group(function () {
    Route::get('/', [RegulationTypeController::class, 'getAll']);
    Route::get('/roots', [RegulationTypeController::class, 'getRoots']);
    Route::get('/tree', [RegulationTypeController::class, 'getTree']);
    Route::get('/{id}', [RegulationTypeController::class, 'get']);
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
    //Regulations
    Route::prefix('/regulations')->middleware('role:author')->group(function () {
        Route::post('/', [RegulationController::class, 'create']);
        Route::put('/{id}', [RegulationController::class, 'update']);
        Route::delete('/{id}', [RegulationController::class, 'delete']);
        Route::delete('/file/{id}', [RegulationController::class, 'deleteFile']);
        Route::delete('/preview-file/{id}', [RegulationController::class, 'deletePreviewFile']);
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
    //Regulation types
    Route::prefix('/regulation-types')->group(function () {
        Route::post('/', [RegulationTypeController::class, 'create'])->middleware('role:author');
        Route::put('/{id}', [RegulationTypeController::class, 'update'])->middleware('role:author');
        Route::delete('/{id}', [RegulationTypeController::class, 'delete'])->middleware('role:author');
    });
    //News
    Route::prefix('/news')->middleware('role:author')->group(function () {
        Route::post('/', [NewsController::class, 'create']);
        Route::put('/{id}', [NewsController::class, 'update']);
        Route::delete('/{id}', [NewsController::class, 'delete']);
        Route::delete('/file/{id}', [NewsController::class, 'deleteFile']);
    });
    //Document
    Route::prefix('/documents')->middleware('role:author')->group(function () {
        Route::post('/', [DocumentController::class, 'create']);
        Route::put('/{id}', [DocumentController::class, 'update']);
        Route::delete('/{id}', [DocumentController::class, 'delete']);
        Route::delete('/file/{id}', [DocumentController::class, 'deleteFile']);
        Route::delete('/preview-file/{id}', [DocumentController::class, 'deletePreviewFile']);
    });
    //Document types
    Route::prefix('/document-types')->middleware('role:author')->group(function () {
        Route::post('/', [DocumentTypeController::class, 'create']);
        Route::put('/{id}', [DocumentTypeController::class, 'update']);
        Route::delete('/{id}', [DocumentTypeController::class, 'delete']);
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
    //Question
    Route::prefix('/questions')->middleware('role:author')->group(function () {
        Route::post('/', [QuestionController::class, 'create']);
        Route::put('/{id}', [QuestionController::class, 'update']);
        Route::delete('/{id}', [QuestionController::class, 'delete']);
        Route::delete('/file/{id}', [QuestionController::class, 'deleteFile']);
    });
    //Question types
    Route::prefix('/question-types')->middleware('role:author')->group(function () {
        Route::post('/', [QuestionTypeController::class, 'create']);
        Route::put('/{id}', [QuestionTypeController::class, 'update']);
        Route::delete('/{id}', [QuestionTypeController::class, 'delete']);
    });
    //Videos
    Route::prefix('/videos')->middleware('role:author')->group(function () {
        Route::post('/', [VideoController::class, 'create']);
        Route::put('/{id}', [VideoController::class, 'update']);
        Route::delete('/{id}', [VideoController::class, 'delete']);
        Route::post('/download-file/{id}', [VideoController::class, 'downloadVideoFile']);
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
