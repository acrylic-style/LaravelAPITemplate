<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->match(['post', 'delete', 'put', 'head', 'trace', 'connect', 'options', 'patch'], '/user', function () {
    return responseError("This route does not accept specified method.", 405);
});

Route::middleware('auth:api')->get('/list', function () {
    $registeredUsers = DB::select('select minecraft, id from users');
    return $registeredUsers;
});

Route::middleware('auth:api')->match(['post', 'delete', 'put', 'head', 'trace', 'connect', 'options', 'patch'], '/list', function () {
    return responseError("This route does not accept specified method.", 405);
});

Route::middleware('auth:api')->post('/add', function (Request $request) {
    $redirect = $request->input('redirect');
    if ($request->getContentType() != 'json') return responseError("You have to use application/json Content-Type.", 400);
    $minecraft = $request->input('minecraft');
    if (empty($minecraft)) {
        return responseError("Parameter 'minecraft' cannot be null.", 400);
    }
    $ids = explode(",", $request->user()->minecraft);
    if (in_array($minecraft, $ids)) return responseError("your specified id was in your minecraft ids list and we couldn't accept your request.", 409);
    $ids = array_merge($ids, [$minecraft]);
    DB::insert("update users set minecraft = '".implode(",", $ids)."' where id = ".$request->user()->id.";");
    if ($redirect == null) {
        return DB::select('select * from users where id = '.$request->user()->id.";");
    } else {
        return redirect()->to($redirect, 200);
    }
});

Route::middleware('auth:api')->post('/remove', function (Request $request) {
    if ($request->getContentType() != 'json') return responseError("You have to use application/json Content-Type.", 400);
    $minecraft = $request->input('minecraft');
    $ids = explode(",", $request->user()->minecraft);
    if (!in_array($minecraft, $ids)) return responseError("your specified id wasn't in your minecraft ids list and we couldn't accept your request.", 418);
    if (($key = array_search($minecraft, $ids)) !== false) {
        unset($ids[$key]);
    } elseif (array_search($minecraft, $ids) === false) {
        return responseError("we tried to find minecraft id with specified value but we couldn't find, weird!", 500);
    }
    $implobed = implode(",", $ids);
    DB::insert("update users set minecraft = '".$implobed."' where id = ".$request->user()->id.";");
    return DB::select('select minecraft, id from users where id = '.$request->user()->id.";");
});

Route::middleware('auth:api')->match(['put', 'delete', 'get', 'head', 'trace', 'connect', 'options', 'patch'], '/register', function () {
    return responseError("This route does not accept specified method.", 405);
});