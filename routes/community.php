<?php

use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\CommunityPostController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('home', [CommunityController::class, 'home']);
    Route::get('delete/{id}', [CommunityController::class, 'delete']);
    Route::post('create', [CommunityController::class, 'create']);
    Route::post('edit', [CommunityController::class, 'edit']);
    Route::get('list/sponsor/{community_id}', [CommunityController::class, 'listSponsor']);
    Route::get('delete/sponsor/{sponsor_id}', [CommunityController::class, 'deleteSponsor']);
    Route::get('delete/picture/{picture_id}', [CommunityController::class, 'deletePicture']);
    Route::post('search', [CommunityController::class, 'search']);
    Route::get('category/search/{cat_id}', [CommunityController::class, 'categorySearch']);
    Route::get('list/{type}', [CommunityController::class, 'list']);
    Route::get('following/list/{community_id}', [CommunityController::class, 'listUser']);
    Route::get('send/invite/{community_id}/{to_id}', [CommunityController::class, 'sendInvite']);
    Route::post('search/users/{community_id}', [CommunityController::class, 'searchUsers']);
    Route::get('invited/communities/', [CommunityController::class, 'InvitedCommunity']);
    Route::post('add/media/', [CommunityController::class, 'addMedia']);
    Route::get('media/delete/{media_id}', [CommunityController::class, 'deleteMedia']);
    Route::post('folder/edit', [CommunityController::class, 'editFolder']);
    Route::post('add/folder', [CommunityController::class, 'addFolder']);
    Route::get('folder/delete/{folder_id}', [CommunityController::class, 'deleteFolder']);
    Route::get('pin/media/{media_id}/{community_id}', [CommunityController::class, 'pinMedia']);
    Route::get('folder/media/{folder_id}', [CommunityController::class, 'folderMedia']);
    Route::get('media/home/{community_id}', [CommunityController::class, 'communitMediaHome']);
    Route::get('media/list/{type}/{community_id}', [CommunityController::class, 'listFolder']);
    Route::get('detail/{communty_id}/{type}/{sub_type?}', [CommunityController::class, 'detail']);
    Route::get('change/user/status/{community_id}/{type}/{user_id}', [CommunityController::class, 'chnageUserStatus']);
    Route::get('list/users/{community_id}/{type}', [CommunityController::class, 'listSimpleUsers']);

    Route::prefix('course')->group(function () {
        Route::post('create', [CommunityController::class, 'createCourse']);
        Route::post('edit', [CommunityController::class, 'editCourse']);
        Route::get('delete/{course_id}', [CommunityController::class, 'deleteCourse']);
        Route::get('detail/{course_id}', [CommunityController::class, 'detailCourse']);
        Route::get('publish/{course_id}', [CommunityController::class, 'publishCourse']);

        Route::get('view/certificate/{course_id}', [CommunityController::class, 'viewCourseCeritificate']);
        Route::post('store/certificate/{crtf_id}', [CommunityController::class, 'storeCourseCeritificate']);

        Route::post('purchase', [CommunityController::class, 'purchaseCourse']);

        Route::prefix('section')->group(function () {
            Route::get('list/{course_id}', [CommunityController::class, 'courseSectionList']);
            Route::post('create', [CommunityController::class, 'createCourseSection']);
            Route::post('edit', [CommunityController::class, 'editCourseSection']);
            Route::get('delete/{section_id}', [CommunityController::class, 'deleteCourseSection']);

            Route::prefix('video')->group(function () {
                Route::get('list/{section_id}', [CommunityController::class, 'listCourseSectionVideos']);
                Route::post('create', [CommunityController::class, 'createCourseSectionVideo']);
                Route::post('edit', [CommunityController::class, 'editCourseSectionVideo']);
                Route::get('delete/{section_id}', [CommunityController::class, 'deleteCourseSectionVideo']);
                Route::get('seen/{video_id}', [CommunityController::class, 'seenSection']);
            });
        });

        Route::get('generate-certificate', [CommunityController::class, 'generateCertificate']);
    });
    Route::prefix('post')->group(function () {
        Route::post('create', [CommunityPostController::class, 'create']);
        Route::get('detail/{post_id}', [CommunityPostController::class, 'detail']);
        Route::get('like/{post_id}', [CommunityPostController::class, 'like']);
        Route::get('like/list/{post_id}', [CommunityPostController::class, 'likeList']);
        Route::get('save/{post_id}', [CommunityPostController::class, 'save']);
        Route::get('delete/{post_id}', [CommunityPostController::class, 'delete']);
        Route::post('comment', [CommunityPostController::class, 'comment']);
        Route::get('comment/delete/{comment_id}', [CommunityPostController::class, 'deleteComment']);
        Route::get('comment/like/{comment_id}', [CommunityPostController::class, 'likeComment']);
        Route::get('comment/list/{post_id}', [CommunityPostController::class, 'commentList']);
        Route::get('comment/replies/{comment_id}', [CommunityPostController::class, 'commentReplies']);
        Route::post('vote', [CommunityPostController::class, 'vote']);

    });
});
