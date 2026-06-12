<?php

declare(strict_types=1);

session_start();

require_once dirname(__DIR__) . '/src/Core/helpers.php';
load_env();
require_once dirname(__DIR__) . '/src/Core/Database.php';
require_once dirname(__DIR__) . '/src/Core/Router.php';
require_once dirname(__DIR__) . '/src/Controller/HomeController.php';
require_once dirname(__DIR__) . '/src/Controller/RideController.php';
require_once dirname(__DIR__) . '/src/Controller/AuthController.php';
require_once dirname(__DIR__) . '/src/Controller/UserController.php';
require_once dirname(__DIR__) . '/src/Controller/EmployeeController.php';
require_once dirname(__DIR__) . '/src/Controller/AdminController.php';
require_once dirname(__DIR__) . '/src/Repository/RideRepository.php';
require_once dirname(__DIR__) . '/src/Repository/AdminRepository.php';
require_once dirname(__DIR__) . '/src/Repository/EmployeeRepository.php';
require_once dirname(__DIR__) . '/src/Repository/UserRepository.php';
require_once dirname(__DIR__) . '/src/Repository/VehicleRepository.php';
require_once dirname(__DIR__) . '/src/Service/UserService.php';

use App\Controller\AuthController;
use App\Controller\AdminController;
use App\Controller\EmployeeController;
use App\Controller\HomeController;
use App\Controller\RideController;
use App\Controller\UserController;
use App\Core\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact', [HomeController::class, 'storeContact']);
$router->get('/mentions-legales', [HomeController::class, 'legal']);
$router->get('/covoiturages', [RideController::class, 'index']);
$router->get('/covoiturages/detail', [RideController::class, 'show']);
$router->get('/covoiturages/participer', [RideController::class, 'confirmParticipation']);
$router->post('/covoiturages/participer', [RideController::class, 'storeParticipation']);
$router->get('/connexion', [AuthController::class, 'login']);
$router->post('/connexion', [AuthController::class, 'authenticate']);
$router->get('/inscription', [AuthController::class, 'register']);
$router->post('/inscription', [AuthController::class, 'store']);
$router->get('/deconnexion', [AuthController::class, 'logout']);
$router->get('/mon-espace', [UserController::class, 'dashboard']);
$router->post('/mon-espace/profil', [UserController::class, 'updateProfile']);
$router->post('/mon-espace/vehicule', [UserController::class, 'storeVehicle']);
$router->post('/mon-espace/voyage', [UserController::class, 'storeRide']);
$router->post('/mon-espace/reservation/annuler', [UserController::class, 'cancelReservation']);
$router->post('/mon-espace/voyage/annuler', [UserController::class, 'cancelDriverRide']);
$router->post('/mon-espace/voyage/demarrer', [UserController::class, 'startDriverRide']);
$router->post('/mon-espace/voyage/arrivee', [UserController::class, 'finishDriverRide']);
$router->post('/mon-espace/reservation/valider', [UserController::class, 'validateReservation']);
$router->post('/mon-espace/reservation/signaler', [UserController::class, 'reportReservation']);
$router->get('/employe', [EmployeeController::class, 'dashboard']);
$router->post('/employe/avis/valider', [EmployeeController::class, 'validateReview']);
$router->post('/employe/avis/refuser', [EmployeeController::class, 'rejectReview']);
$router->post('/employe/incidents/resoudre', [EmployeeController::class, 'resolveIncident']);
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->post('/admin/employes', [AdminController::class, 'storeEmployee']);
$router->post('/admin/comptes/suspendre', [AdminController::class, 'suspendAccount']);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
