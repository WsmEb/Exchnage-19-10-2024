<?php

use App\Http\Controllers\actionComptable;
use App\Http\Controllers\adminDashboardController;
use App\Http\Controllers\ConnexionClientController;
use App\Http\Controllers\ConvertsController;
use App\Http\Controllers\dashbordControoler;
use App\Http\Controllers\DeposeController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DeviseController;
use App\Http\Controllers\PendingActionController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HistoriqueOperationsController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\TransfertController;
use Illuminate\Support\Facades\Route;



Route::get('/', [adminDashboardController::class, 'index']);

Route::put('/entreprise/{titre}', [EntrepriseController::class, 'update']);

Route::get('/get-base-devise/{symbol}', [DeviseController::class, 'getBaseDevise']);
Route::post('/update-base-devise', [DeviseController::class, 'updateBaseDevise']);
Route::get('/Devise', [DeviseController::class, 'index'])->name('devise.index');
Route::post('/Devise', [DeviseController::class, 'add'])->name('devise.add');
Route::delete('/Devise', [DeviseController::class, 'delete'])->name('devise.delete');
Route::put('/Devise', [DeviseController::class, 'update'])->name('devise.update');
Route::get('/Devise/search/{devise?}', [DeviseController::class, "search"])->name("devise.search");
Route::get('/Devise/export', [DeviseController::class, 'export'])->name("devise.excel");

Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
Route::post('/clients', [ClientController::class, 'add'])->name('clients.add');
Route::delete('/clients', [ClientController::class, 'delete'])->name('clients.delete');
Route::put('/clients', [ClientController::class, 'update'])->name('clients.update');
Route::post('/clients/password', [ClientController::class, 'update_password'])->name('clients.update_password');
Route::post('/clients/verrouiller', [ClientController::class, 'update_verrouiller'])->name('clients.update_verrouiller');
Route::get('/clients/search/{client?}', [ClientController::class, "search"])->name("clients.search");
Route::get('/clients/searchsolde/{type?}', [ClientController::class, "searchbySolde"])->name("clients.searchsolde");
Route::post('/clients/operation', [ClientController::class, 'operation'])->name('clients.operation');
Route::get('/clients/export', [ClientController::class, 'export'])->name("clients.excel");
Route::get('/clients/pdf', [ClientController::class, 'exportPDF'])->name("clients.pdf");

Route::get('/comptables', [UserController::class, 'index'])->name('comptables.index');
Route::post('/comptables', [UserController::class, 'add'])->name('comptables.add');
Route::delete('/comptables', [UserController::class, 'delete'])->name('comptables.delete');
Route::post('/comptables/deconnecter', [UserController::class, 'deconnexion'])->name('comptables.deconnecter');
Route::put('/comptables', [UserController::class, 'update'])->name('comptables.update');
Route::post('/comptables/password', [UserController::class, 'update_password'])->name('comptables.update_password');
Route::post('/comptables/verrouiller', [UserController::class, 'update_verrouiller'])->name('comptables.update_verrouiller');
Route::get('/users/permissionclients/{userId}', [UserController::class, 'permission_clients'])->name('users.permissionclients');
Route::post('/users/permissionclients', [UserController::class, 'add_permission_clients'])->name('users.addpermissionclients');
Route::get('/users/permissionpages/{userId}', [UserController::class, 'permission_pages'])->name('users.permissionpages');
Route::post('/users/permissionpages', [UserController::class, 'add_permission_pages'])->name('users.addpermissionpages');


Route::get('/users/permissionactions/{userId}/{page}', [UserController::class, 'permission_actions'])->name('users.permissionactions');
Route::post('/users/permissionactions', [UserController::class, 'add_permission_actions'])->name('users.addpermissionactions');

Route::get('/login', [UserController::class, 'connexion'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('connexion');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('/operations/{client}/{devise}', [OperationController::class, 'index'])->name('operation.index');
Route::post('/operations', [OperationController::class, 'add'])->name('operation.add');
Route::delete('/operations', [OperationController::class, 'delete'])->name('operation.delete');
Route::put('/operations', [OperationController::class, 'update'])->name('operation.update');
Route::get('/operations/search/{client}/{devise}/{col?}/{val?}', [OperationController::class, "search"])->name("operation.search");
Route::get('/operations/date/{client}/{devise}/{date1?}/{date2?}', [OperationController::class, "searchdate"])->name("operation.date");
Route::get('/operations/exel', [OperationController::class, 'excel'])->name("operation.excel");
Route::get('/operations/pdf', [OperationController::class, 'pdf'])->name("operation.pdf");

Route::get('/transferts/{client}/{devise}', [TransfertController::class, 'index'])->name('transfert.index');
Route::post('/transferts', [TransfertController::class, 'add'])->name('transfert.add');
Route::delete('/transferts', [TransfertController::class, 'delete'])->name('transfert.delete');
Route::put('/transferts', [TransfertController::class, 'update'])->name('transfert.update');
Route::get('/transferts/search/{client}/{devise}/{col?}', [TransfertController::class, "search"])->name("transfert.search");
Route::get('/transferts/date/{client}/{devise}/{date1?}/{date2?}', [TransfertController::class, "searchdate"])->name("transfert.date");
Route::get('/transferts/exel', [TransfertController::class, 'excel'])->name("transfert.excel");
Route::get('/transferts/pdf', [TransfertController::class, 'pdf'])->name("transfert.pdf");

Route::get("/converts/{client}/{devise}", [ConvertsController::class, "index"])->name("converts.index");
Route::post("/converts", [ConvertsController::class, "add"])->name("converts.add");
Route::put("/converts", [ConvertsController::class, "update"])->name("converts.update");
Route::delete("/converts", [ConvertsController::class, "delete"])->name("converts.delete");
Route::get('/converts/search/{client}/{devise}/{col?}/{val?}', [ConvertsController::class, "search"])->name("converts.search");
Route::get('/converts/date/{client}/{devise}/{date1?}/{date2?}', [ConvertsController::class, "searchdate"])->name("converts.date");
Route::get('/converts/exel', [ConvertsController::class, 'excel'])->name("converts.excel");
Route::get('/converts/pdf', [ConvertsController::class, 'pdf'])->name("converts.pdf");
Route::get('/converts/find/{devise}/', [ConvertsController::class, 'findByDevise'])->name("converts.findByDevise");
// Route::get('/converts/func/{deviseOrigin}/{convertedDevise}', [ConvertsController::class, 'JsConvertedSymbol'])->name("converts.JsConvertedSymbol");
Route::get('/converts/func/{deviseOrigin}/{convertedDevise}', [ConvertsController::class, 'JsConvertedSymbols'])->name("converts.JsConvertedSymbol");

Route::get("/deposes/{client}/{devise}", [DeposeController::class, "index"])->name("deposes.index");
Route::get("/deposes/search/{client}/{devise}/{col?}/{val?}", [DeposeController::class, "search"])->name("deposes.search");
Route::get("/deposes/date/{client}/{devise}/{date1?}/{date2?}", [DeposeController::class, "searchdate"])->name("deposes.searchdate");
Route::post("/deposes", [DeposeController::class, "add"])->name("deposes.add");
Route::put("/deposes", [DeposeController::class, "update"])->name("deposes.update");
Route::get("/deposes/excel", [DeposeController::class, "excel"])->name("deposes.excel");
Route::get("/deposes/pdf", [DeposeController::class, "pdf"])->name("deposes.pdf");
Route::delete("/deposes", [DeposeController::class, "delete"])->name("deposes.delete");

Route::get("/profile", [profileController::class, "index"])->name("profile.index");
Route::put("/profile", [profileController::class, "update"])->name("profile.update");
Route::get("/profile/user", [profileController::class, "userIndex"])->name("profile.userIndex");
Route::put("/profile/user", [profileController::class, "userUpdate"])->name("profile.userUpdate");
Route::put("/profile/user/updatePassword", [profileController::class, "update_password"])->name("profile.update_password");

Route::get('/404', function () {
    return view('404');
})->name('404');

Route::get('/403', function () {
    return view('403');
})->name('403');

Route::get('/ExportPDF', [PDFController::class, 'exportPDF'])->name("export.pdf");


Route::get('/totaldesvises/{client}', [ClientController::class, 'SoldeDevisesByClients']);




Route::get("/stock/", [StockController::class, "index"])->name("stock.index");
Route::get("/stock/pdf", [StockController::class, "pdf"])->name("stock.pdf");
Route::get("/stock/excel", [StockController::class, "excel"])->name("stock.excel");
Route::get("/stock/recherche/{val?}", [StockController::class, "search"])->name("search.recherche");
Route::get("/stock/detail/{client?}", [StockController::class, "DetailClientenStock"])->name("stock.detail");




Route::get('/historique', [HistoriqueOperationsController::class, "index"])->name("historique.index");
Route::get('/historique/search/{col?}/{val?}', [HistoriqueOperationsController::class, "searchbycollonne"])->name("historique.search");
Route::get('/historique/date/{datedebut?}/{datefin?}', [HistoriqueOperationsController::class, "searchbydate"])->name("historique.date");
Route::delete('/historique', [HistoriqueOperationsController::class, 'delete'])->name('historique.delete');
Route::put('/historique', [HistoriqueOperationsController::class, 'update'])->name('historique.update');


Route::post('/historique/add-operations', [HistoriqueOperationsController::class, "operations_add"]);
Route::post('/historique/addexiste-operations', [HistoriqueOperationsController::class, "operations_addexiste"]);
Route::get('/historiquesoperations/{id}', [HistoriqueOperationsController::class, 'operations_show'])->name('historiqueoperations.show');
Route::delete('/historiquesoperations/detail', [HistoriqueOperationsController::class, 'operations_deletedetail'])->name('detailhistoriqueoperations.delete');
Route::post('/historiquesoperations/detail', [HistoriqueOperationsController::class, 'operations_restoredetail'])->name('detailhistoriqueoperations.restore');


Route::post('/historique/add-transferts', [HistoriqueOperationsController::class, "transferts_add"]);
Route::post('/historique/addexiste-transferts', [HistoriqueOperationsController::class, "transferts_addexiste"]);
Route::get('/historiquestransferts/{id}', [HistoriqueOperationsController::class, 'transferts_show'])->name('historiquetransferts.show');
Route::delete('/historiquestransferts/detail', [HistoriqueOperationsController::class, 'transferts_deletedetail'])->name('detailhistoriquetransferts.delete');
Route::post('/historiquestransferts/detail', [HistoriqueOperationsController::class, 'transferts_restoredetail'])->name('detailhistoriquetransferts.restore');

//CONVERTES 

Route::post('/historique/add-convertes', [HistoriqueOperationsController::class, "convertes_add"]);
Route::post('/historique/addexiste-convertes', [HistoriqueOperationsController::class, "convertes_addexiste"]);
Route::get('/historiquesconvertes/{id}', [HistoriqueOperationsController::class, 'convertes_show'])->name('historiqueconvertes.show');
Route::delete('/historiquesconvertes/detail', [HistoriqueOperationsController::class, 'convertes_deletedetail'])->name('detailhistoriqueconvertes.delete');
Route::post('/historiquesconvertes/detail', [HistoriqueOperationsController::class, 'convertes_restoredetail'])->name('detailhistoriqueconvertes.restore');



Route::post('/historique/add-deposers', [HistoriqueOperationsController::class, "deposers_add"]);
Route::post('/historique/addexiste-deposers', [HistoriqueOperationsController::class, "deposers_addexiste"]);
Route::get('/historiquesdeposers/{id}', [HistoriqueOperationsController::class, 'deposers_show'])->name('historiquedeposers.show');
Route::delete('/historiquesdeposers/detail', [HistoriqueOperationsController::class, 'deposers_deletedetail'])->name('detailhistoriquedeposers.delete');
Route::post('/historiquesdeposers/detail', [HistoriqueOperationsController::class, 'deposers_restoredetail'])->name('detailhistoriquedeposers.restore');

Route::get("/Admin/dashbord", [adminDashboardController::class, "index"])->name("admin.dashboard");


Route::middleware('auth')->group(function () {
    Route::get('/admin/pending-actions', [PendingActionController::class, 'index'])->name('admin.pending-actions.index');
    Route::post('/admin/pending-actions/approve/{id}', [PendingActionController::class, 'approve'])->name('admin.pending-actions.approve');
    Route::post('/admin/pending-actions/reject/{id}', [PendingActionController::class, 'reject'])->name('admin.pending-actions.reject');
    Route::post('/admin/pending-actions/hover/{id}', [PendingActionController::class, 'hover'])->name('admin.pending-actions.hover');
});


Route::get('/client/login', [ConnexionClientController::class, 'connexion'])->name('client.login');
Route::post('/client/login', [ConnexionClientController::class, 'login'])->name('client.connexion');
Route::get('/client/logout', [ConnexionClientController::class, 'logout'])->name('client.logout');
Route::get('/client/dashboard/{client}', [ConnexionClientController::class, 'dashboard'])->name('client.dashboard');



// Route::get("/dashbord",[dashbordControoler::class,"index"])->name("dashbord.index");
// Route::get("/actionComptable",[actionComptable::class,"index"])->name("actionComptable.index");
