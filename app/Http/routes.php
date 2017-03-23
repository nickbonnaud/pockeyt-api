<?php

Route::get('/', 'AppController@index')->name('app.index');
Route::get('data_use_policy', 'AppController@policy')->name('app.policy');

// Auth routes...
Route::get('auth/login',        'Auth\AuthController@getLogin')->name('auth.login');
Route::post('auth/login',       'Auth\AuthController@postLogin');
Route::get('auth/logout',       'Auth\AuthController@getLogout')->name('auth.logout');
Route::get('auth/register',     'Auth\AuthController@getRegister')->name('auth.register');
Route::post('auth/register',    'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail')->name('password.email');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// Posts routes...
Route::post('posts/{posts}/photos', 'PostsController@postPhotos')->name('posts.photos');
Route::get('posts/list', 'PostsController@listPosts')->name('posts.list');
Route::get('posts/events', 'PostsController@eventPosts')->name('posts.events');
Route::post('events/store', 'PostsController@storeEvent')->name('events.store');
Route::get('events/{posts}', 'PostsController@showEvent')->name('events.show');

Route::get('posts/deals', 'PostsController@dealPosts')->name('posts.deals');
Route::post('deals/store', 'PostsController@storeDeal')->name('deals.store');

Route::resource('posts', 'PostsController', ['only' => ['index', 'store', 'show', 'destroy']]);

// Products routes
Route::get('products/list', 'ProductsController@listProducts')->name('products.list');
Route::post('products/{products}/photos', 'ProductsController@postPhotos')->name('products.photos');
Route::get('products/inventory/{profiles}', 'ProductsController@getInventory');
Route::post('products/square/connect', 'ProductsController@connectSquare');
Route::get('products/square/connect', 'ProductsController@connectSquare');
Route::get('products/square/sync', 'ProductsController@syncSquareItems');
Route::resource('products', 'ProductsController', ['only' => ['store', 'destroy', 'edit', 'update']]);

// Profile routes...
Route::post('profiles/{profiles}/photos', 'ProfilesController@postPhotos')->name('profiles.photos');
Route::delete('profiles/{profiles}/photos', 'ProfilesController@deletePhotos');
Route::post('profiles/{profiles}/approve', 'ProfilesController@postApprove')->name('profiles.approve');
Route::post('profiles/{profiles}/unapprove', 'ProfilesController@postUnapprove')->name('profiles.unapprove');
Route::post('profiles/{profiles}/feature', 'ProfilesController@postFeature')->name('profiles.feature');
Route::post('profiles/{profiles}/unfeature', 'ProfilesController@postUnfeature')->name('profiles.unfeature');
Route::patch('profiles/{profiles}/location', 'ProfilesController@changeLocation')->name('profiles.location');
Route::patch('profiles/{profiles}/tags', 'ProfilesController@changeTags')->name('profiles.tags');
Route::resource('profiles', 'ProfilesController');

// Blog routes
Route::resource('blogs', 'BlogsController');

//Dashboard user Routes
Route::post('users/{users}/photos', 'BusinessUsersController@postPhotos')->name('users.photos');
Route::patch('users/{users}/credentials','BusinessUsersController@changePassword')->name('users.credentials');
Route::resource('users', 'BusinessUsersController');

//Payment Account Routes
Route::patch('accounts/{accounts}/personal', 'AccountsController@changePersonal')->name('accounts.personal');
Route::patch('accounts/{accounts}/business', 'AccountsController@changeBusiness')->name('accounts.business');
Route::patch('accounts/{accounts}/pay', 'AccountsController@changePay')->name('accounts.pay');
Route::post('accounts/status', 'AccountsController@postStatus');
Route::resource('accounts', 'AccountsController');

// Transaction Routes
Route::get('bill/{customerId}', 'TransactionsController@showBill')->name('bill.show');
Route::post('bill', 'TransactionsController@store')->name('bill.store');
Route::patch('bill/{transactionId}', 'TransactionsController@update')->name('bill.update');
Route::post('bill/charge', 'TransactionsController@charge')->name('bill.charge');
Route::patch('bill/charge/{transactionId}', 'TransactionsController@chargeExisting')->name('bill.chargeExisting');
Route::post('purchased/deals', 'TransactionsController@getPurchased');
Route::post('user/purchases', 'TransactionsController@getUserPurchases');
Route::post('user/deals', 'TransactionsController@getUserDeals');
Route::post('user/deal/redeem', 'TransactionsController@redeemUserDeal');
Route::post('business/transactions', 'TransactionsController@getTransactions');
Route::post('business/transactions/finalized', 'TransactionsController@getFinalizedTransactions');


// Connect Routes
Route::get('connect/facebook', 'ConnectController@connectFB');
Route::get('connect/subscribe/facebook', 'ConnectController@verifySubscribeFB');
Route::post('connect/subscribe/facebook', 'ConnectController@receiveFBFeed');

Route::get('connect/instagram', 'ConnectController@connectInsta');
Route::get('connect/subscribe/instagram', 'ConnectController@verifySubscribeInsta');
Route::post('connect/subscribe/instagram', 'ConnectController@receiveInstaMedia');

// Loyalty Programs
Route::resource('loyalty-programs', 'LoyaltyProgramsController');
Route::group(['prefix' => 'api'], function() {
    Route::get('loyalty/cards', 'LoyaltyProgramsController@getLoyaltyCards');
});


// JWT Authentication routes
Route::group(['prefix' => 'api'], function() {
    Route::post('register', 'AuthenticateController@register');
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::put('update', 'AuthenticateController@update');
    Route::post('facebook', 'AuthenticateController@facebook');
    
});

// API User Routes
Route::group(['prefix' => 'api'], function() {
    Route::get('authenticate/user', 'UsersController@getAuthenticatedUser');
    Route::put('authenticate/user', 'UsersController@updateAuthenticatedUser');
    Route::delete('authenticate/user', 'UsersController@destroyAuthenticatedUser');

    Route::post('authenticate/user/photo', 'UsersController@postPhoto');
    Route::delete('authenticate/user/photo', 'UsersController@deletePhoto');

    Route::post('set/tip', 'UsersController@setDefaultTipRate');
    Route::post('token/refresh', 'UsersController@refreshToken');
});


// Push Ids Routes
Route::group(['prefix' => 'api'], function() {
    Route::post('token/push', 'PushIdsController@store');
    Route::post('token/sync', 'PushIdsController@sync');
});


// Payment Routes
Route::group(['prefix' => 'api'], function() {
    Route::get('token/client', 'PaymentController@clientToken');
    Route::post('customer', 'PaymentController@createCustomer');
    Route::put('customer', 'PaymentController@editPaymentMethod');
});

//geo routes
Route::group(['prefix' => 'api'], function() {
    Route::post('geo', 'GeoController@postLocationMonitor');
    Route::get('geo/fences', 'GeoController@getGeoFences');
});
Route::post('geo/user/destroy', 'GeoController@deleteInactiveUser')->name('inactiveUser.delete');

//transaction routes
Route::group(['prefix' => 'api'], function() {
    Route::put('transaction/accept', 'TransactionsController@userConfirmBill');
    Route::put('transaction/decline', 'TransactionsController@userDeclineBill');
    Route::put('transaction/custom', 'TransactionsController@customTip');
    Route::get('transaction/show', 'TransactionsController@getCurrentBill');
    Route::get('transaction/open', 'TransactionsController@hasBill');
    Route::post('transaction/bill', 'TransactionsController@requestBill');
    Route::post('transaction/deal', 'TransactionsController@purchaseDeal');
    Route::get('transactions/recent', 'TransactionsController@getRecentTransactions');
    Route::get('transactions/deals', 'TransactionsController@getDeals');
});

//Analytics routes
Route::group(['prefix' => 'api'], function() {
    Route::post('analytics/posts/viewed', 'AnalyticsController@viewedPosts');
    Route::post('analytics/posts/interaction', 'AnalyticsController@interactionPosts');
});

//Quickbook Routes
Route::get('qbo/oauth','QuickBookController@qboOauth');
Route::get('qbo/success','QuickBookController@qboSuccess');
Route::get('qbo/tax', 'QuickBookController@qboTax')->name('qbo.tax');
Route::get('qbo/disconnect','QuickBookController@qboDisconnect');
Route::post('sync/invoice', 'QuickBookController@syncInvoice')->name('sync.invoice');



//API routes V2
Route::group(['prefix' => 'api/v2'], function() {
    Route::get('profiles', 'APIController@getProfilesV2');
    Route::get('posts', 'APIController@getPostsV2');
    Route::get('favs', 'APIController@getFavsV2');
    Route::get('search', 'APIController@getSearchV2');
    Route::get('events', 'APIController@getEventsV2');
    Route::get('bookmarks', 'APIController@getBookmarksV2');
    Route::get('business/posts', 'APIController@getBusinessPostsV2');
});



// API Routes V1
Route::controller('api', 'APIController', [
    'getPosts' => 'api.posts',
    'getPost' => 'api.post',
    'getProfiles' => 'api.profiles',
    'getProfile' => 'api.profile',

    'getProfilesv1' => 'api.profilesv1',
    'getpostsv1' => 'api.postsv1',
    'getfavs' => 'api.favs',
    'getsearch' => 'api.search',
    'getEvents' => 'api.events',
    'getBlogs' => 'api.blogs',
    'getBookmarks' => 'api.bookmarks',
]);