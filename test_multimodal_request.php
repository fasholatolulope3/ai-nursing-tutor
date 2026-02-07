<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a dummy user
$user = \App\Models\User::first();
auth()->login($user);

// Create a dummy PDF file
$dummyPdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << >> /MediaBox [0 0 612 792] >>\nendobj\nxref\n0 4\n0000000000 65535 f \n0000000010 00000 n \n0000000060 00000 n \n0000000157 00000 n \ntrailer\n<< /Size 4 /Root 1 0 R >>\nstartxref\n260\n%%EOF";
$tempFilePath = sys_get_temp_dir() . '/test_doc.pdf';
file_put_contents($tempFilePath, $dummyPdfContent);

$file = new \Illuminate\Http\UploadedFile(
    $tempFilePath,
    'test_doc.pdf',
    'application/pdf',
    null,
    true
);

echo "Testing Multimodal Query with PDF...\n";

// Instantiate Controller directly
try {
    $controller = $app->make(\App\Http\Controllers\Api\V1\SimulationController::class);

    // Mock Request
    $request = \Illuminate\Http\Request::create(
        '/api/v1/simulations/clinical-query',
        'POST',
        [
            'message' => 'Analyze this document.'
        ],
        [],
        [
            'attachment' => $file
        ]
    );
    $request->setUserResolver(fn() => $user);

    $response = $controller->handleClinicalQuery($request);

    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";

    if ($response->getStatusCode() === 200) {
        echo "VERIFICATION PASSED\n";
    } else {
        echo "VERIFICATION FAILED\n";
    }
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
