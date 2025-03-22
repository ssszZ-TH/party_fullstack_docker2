<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Controllers\UserController;
use App\Controllers\MaritalstatustypeController;
use App\Controllers\PersonnametypeController;
use App\Controllers\PhysicalcharacteristictypeController;
use App\Controllers\PhysicalcharacteristicController;
use App\Controllers\CountryController;
use App\Controllers\MaritalstatusController;
use App\Controllers\PersonnameController;
use App\Controllers\CitizenshipController;
use App\Controllers\PassportController;
use App\Controllers\PersonController;
use App\Controllers\PartytypeController;
use App\Controllers\partyclassificationController;
use App\Controllers\LegalorganizationController;
use App\Controllers\Informal_organizationController;
use App\Controllers\EthnicityController;
use App\Controllers\IncomerangeController;
use App\Controllers\IndustrytypeController;
use App\Controllers\EmployeecountrangeController;
use App\Controllers\MinoritytypeController;
use App\Controllers\Classify_by_eeocController;


require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// ทำ body parser ใช้เอง เพื่อความสดวก
$app->add(function (Request $request, $handler) {
    $contentType = $request->getHeaderLine('Content-Type');
    if (stripos($contentType, 'application/json') !== false) {
        $rawBody = $request->getBody()->getContents();
        $parsedBody = json_decode($rawBody, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $request = $request->withParsedBody($parsedBody);
        }
    }
    return $handler->handle($request);
});

// test
$app->get('/test', function (Request $request, Response $response): Response {
    $test = ['message' => 'spa use slim framework'];
    $response->getBody()->write(json_encode($test));
    return $response->withHeader('Content-Type', 'application/json');
});

// Routes สำหรับ User
$app->get('/users', [UserController::class, 'list']);
$app->get('/users/{id}', [UserController::class, 'get']);
$app->post('/users', [UserController::class, 'create']);
$app->put('/users/{id}', [UserController::class, 'update']);
$app->delete('/users/{id}', [UserController::class, 'delete']);

// Routes สำหรับ User
$app->get('/v1/maritalstatustype', [MaritalstatustypeController::class, 'list']);
$app->get('/v1/maritalstatustype/{id}', [MaritalstatustypeController::class, 'get']);
$app->post('/v1/maritalstatustype', [MaritalstatustypeController::class, 'create']);
$app->put('/v1/maritalstatustype/{id}', [MaritalstatustypeController::class, 'update']);
$app->delete('/v1/maritalstatustype/{id}', [MaritalstatustypeController::class, 'delete']);

// Routes สำหรับ User
$app->get('/v1/personnametype', [PersonnametypeController::class, 'list']);
$app->get('/v1/personnametype/{id}', [PersonnametypeController::class, 'get']);
$app->post('/v1/personnametype', [PersonnametypeController::class, 'create']);
$app->put('/v1/personnametype/{id}', [PersonnametypeController::class, 'update']);
$app->delete('/v1/personnametype/{id}', [PersonnametypeController::class, 'delete']);

// Routes สำหรับ User
$app->get('/v1/physicalcharacteristictype', [PhysicalcharacteristictypeController::class, 'list']);
$app->get('/v1/physicalcharacteristictype/{id}', [PhysicalcharacteristictypeController::class, 'get']);
$app->post('/v1/physicalcharacteristictype', [PhysicalcharacteristictypeController::class, 'create']);
$app->put('/v1/physicalcharacteristictype/{id}', [PhysicalcharacteristictypeController::class, 'update']);
$app->delete('/v1/physicalcharacteristictype/{id}', [PhysicalcharacteristictypeController::class, 'delete']);

$app->get('/v1/country', [CountryController::class, 'list']);
$app->get('/v1/country/{id}', [CountryController::class, 'get']);
$app->post('/v1/country', [CountryController::class, 'create']);
$app->put('/v1/country/{id}', [CountryController::class, 'update']);
$app->delete('/v1/country/{id}', [CountryController::class, 'delete']);

$app->get('/v1/maritalstatus', [MaritalstatusController::class, 'list']);
$app->get('/v1/maritalstatus/{id}', [MaritalstatusController::class, 'get']);
$app->post('/v1/maritalstatus', [MaritalstatusController::class, 'create']);
$app->put('/v1/maritalstatus/{id}', [MaritalstatusController::class, 'update']);
$app->delete('/v1/maritalstatus/{id}', [MaritalstatusController::class, 'delete']);

$app->get('/v1/personname', [PersonnameController::class, 'list']);
$app->get('/v1/personname/{id}', [PersonnameController::class, 'get']);
$app->post('/v1/personname', [PersonnameController::class, 'create']);
$app->put('/v1/personname/{id}', [PersonnameController::class, 'update']);
$app->delete('/v1/personname/{id}', [PersonnameController::class, 'delete']);

$app->get('/v1/citizenship', [CitizenshipController::class, 'list']);
$app->get('/v1/citizenship/{id}', [CitizenshipController::class, 'get']);
$app->post('/v1/citizenship', [CitizenshipController::class, 'create']);
$app->put('/v1/citizenship/{id}', [CitizenshipController::class, 'update']);
$app->delete('/v1/citizenship/{id}', [CitizenshipController::class, 'delete']);

$app->get('/v1/passport', [PassportController::class, 'list']);
$app->get('/v1/passport/{id}', [PassportController::class, 'get']);
$app->post('/v1/passport', [PassportController::class, 'create']);
$app->put('/v1/passport/{id}', [PassportController::class, 'update']);
$app->delete('/v1/passport/{id}', [PassportController::class, 'delete']);

$app->get('/v1/person', [PersonController::class, 'list']);
$app->get('/v1/person/{id}', [PersonController::class, 'get']);
$app->post('/v1/person', [PersonController::class, 'create']);
$app->put('/v1/person/{id}', [PersonController::class, 'update']);
$app->delete('/v1/person/{id}', [PersonController::class, 'delete']);

$app->get('/v1/partytype', [PartytypeController::class, 'list']);
$app->get('/v1/partytype/{id}', [PartytypeController::class, 'get']);
$app->post('/v1/partytype', [PartytypeController::class, 'create']);
$app->put('/v1/partytype/{id}', [PartytypeController::class, 'update']);
$app->delete('/v1/partytype/{id}', [PartytypeController::class, 'delete']);

$app->get('/v1/partyclassification', [partyclassificationController::class, 'list']);
$app->get('/v1/partyclassification/{id}', [partyclassificationController::class, 'get']);
$app->post('/v1/partyclassification', [partyclassificationController::class, 'create']);
$app->put('/v1/partyclassification/{id}', [partyclassificationController::class, 'update']);
$app->delete('/v1/partyclassification/{id}', [partyclassificationController::class, 'delete']);

$app->get('/v1/legalorganization', [LegalorganizationController::class, 'list']);
$app->get('/v1/legalorganization/{id}', [LegalorganizationController::class, 'get']);
$app->post('/v1/legalorganization', [LegalorganizationController::class, 'create']);
$app->put('/v1/legalorganization/{id}', [LegalorganizationController::class, 'update']);
$app->delete('/v1/legalorganization/{id}', [LegalorganizationController::class, 'delete']);

$app->get('/v1/physicalcharacteristic', [PhysicalcharacteristicController::class, 'list']);
$app->get('/v1/physicalcharacteristic/{id}', [PhysicalcharacteristicController::class, 'get']);
$app->post('/v1/physicalcharacteristic', [PhysicalcharacteristicController::class, 'create']);
$app->put('/v1/physicalcharacteristic/{id}', [PhysicalcharacteristicController::class, 'update']);
$app->delete('/v1/physicalcharacteristic/{id}', [PhysicalcharacteristicController::class, 'delete']);

$app->get('/v1/informalorganization', [Informal_organizationController::class, 'list']);
$app->get('/v1/informalorganization/{id}', [Informal_organizationController::class, 'get']);
$app->post('/v1/informalorganization', [Informal_organizationController::class, 'create']);
$app->put('/v1/informalorganization/{id}', [Informal_organizationController::class, 'update']);
$app->delete('/v1/informalorganization/{id}', [Informal_organizationController::class, 'delete']);

$app->get('/v1/ethnicity', [EthnicityController::class, 'list']);
$app->get('/v1/ethnicity/{id}', [EthnicityController::class, 'get']);
$app->post('/v1/ethnicity', [EthnicityController::class, 'create']);
$app->put('/v1/ethnicity/{id}', [EthnicityController::class, 'update']);
$app->delete('/v1/ethnicity/{id}', [EthnicityController::class, 'delete']);

$app->get('/v1/incomerange', [IncomerangeController::class, 'list']);
$app->get('/v1/incomerange/{id}', [IncomerangeController::class, 'get']);
$app->post('/v1/incomerange', [IncomerangeController::class, 'create']);
$app->put('/v1/incomerange/{id}', [IncomerangeController::class, 'update']);
$app->delete('/v1/incomerange/{id}', [IncomerangeController::class, 'delete']);

$app->get('/v1/industrytype', [IndustrytypeController::class, 'list']);
$app->get('/v1/industrytype/{id}', [IndustrytypeController::class, 'get']);
$app->post('/v1/industrytype', [IndustrytypeController::class, 'create']);
$app->put('/v1/industrytype/{id}', [IndustrytypeController::class, 'update']);
$app->delete('/v1/industrytype/{id}', [IndustrytypeController::class, 'delete']);

$app->get('/v1/employeecountrange', [EmployeecountrangeController::class, 'list']);
$app->get('/v1/employeecountrange/{id}', [EmployeecountrangeController::class, 'get']);
$app->post('/v1/employeecountrange', [EmployeecountrangeController::class, 'create']);
$app->put('/v1/employeecountrange/{id}', [EmployeecountrangeController::class, 'update']);
$app->delete('/v1/employeecountrange/{id}', [EmployeecountrangeController::class, 'delete']);

$app->get('/v1/minoritytype', [MinoritytypeController::class, 'list']);
$app->get('/v1/minoritytype/{id}', [MinoritytypeController::class, 'get']);
$app->post('/v1/minoritytype', [MinoritytypeController::class, 'create']);
$app->put('/v1/minoritytype/{id}', [MinoritytypeController::class, 'update']);
$app->delete('/v1/minoritytype/{id}', [MinoritytypeController::class, 'delete']);

$app->get('/v1/classifybyeeoc', [Classify_by_eeocController::class, 'list']);
$app->get('/v1/classifybyeeoc/{id}', [Classify_by_eeocController::class, 'get']);
$app->post('/v1/classifybyeeoc', [Classify_by_eeocController::class, 'create']);
$app->put('/v1/classifybyeeoc/{id}', [Classify_by_eeocController::class, 'update']);
$app->delete('/v1/classifybyeeoc/{id}', [Classify_by_eeocController::class, 'delete']);

// // Routes สำหรับ Product
// $app->get('/products', [ProductController::class, 'list']);
// $app->get('/products/{id}', [ProductController::class, 'get']);
// $app->post('/products', [ProductController::class, 'create']);
// $app->put('/products/{id}', [ProductController::class, 'update']);
// $app->delete('/products/{id}', [ProductController::class, 'delete']);

// เพิ่ม routes อีก 5 ตัวตามลักษณะนี้

$app->run();