<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        try {
            $client = new Client();

            // Build query parameters
            $queryParams = [];

            // Pagination parameters
            if ($request->has('page')) {
                $queryParams['page'] = $request->get('page');
            }

            if ($request->has('per_page')) {
                $queryParams['per_page'] = $request->get('per_page');
            }

            // Search parameter
            if ($request->has('search') && !empty($request->get('search'))) {
                $queryParams['search'] = $request->get('search');
            }

            // Individual filter parameters - API supports direct parameter names
            $filterParams = ['gender', 'ethnic', 'education', 'married_status', 'job', 'blood_type'];
            foreach ($filterParams as $param) {
                if ($request->has($param) && !empty($request->get($param))) {
                    $queryParams[$param] = $request->get($param);
                }
            }

            $response = $client->request('GET', 'https://mockapi.pkuwsb.id/api/patient', [
                'headers' => [
                    'X-username' => 'admin',
                    'X-password' => 'secret',
                    'Accept' => 'application/json',
                ],
                'query' => $queryParams,
                'timeout' => 30,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $patients = $data['success'] ? $data['data']['data'] : [];
            $pagination = $data['success'] ? [
                'current_page' => $data['data']['current_page'],
                'last_page' => $data['data']['last_page'],
                'per_page' => $data['data']['per_page'],
                'total' => $data['data']['total'],
                'from' => $data['data']['from'],
                'to' => $data['data']['to'],
                'links' => $data['data']['links'],
                'next_page_url' => $data['data']['next_page_url'],
                'prev_page_url' => $data['data']['prev_page_url'],
            ] : [];

        } catch (RequestException $e) {
            $patients = [];
            $pagination = [];
        }

        return Inertia('patient/index', [
            'patients' => $patients,
            'pagination' => $pagination,
            'filters' => [
                'search' => $request->get('search'),
                'gender' => $request->get('gender'),
                'ethnic' => $request->get('ethnic'),
                'education' => $request->get('education'),
                'married_status' => $request->get('married_status'),
                'job' => $request->get('job'),
                'blood_type' => $request->get('blood_type'),
            ]
        ]);
    }

    public function storePatient(Request $request)
    {
        try {
            $client = new Client();

            // Generate or use provided RM number
            $rmNumber = $request->rm_number ?? $this->generateRandomRMNumber();

            // Handle file upload for avatar
            $avatarUrl = 'https://api.dicebear.com/6.x/identicon/svg?seed=' . strtolower($request->first_name . '.' . $request->last_name);

            // Prepare data for API - fix the ethnic field to be JSON string
            $apiData = [
                'rm_number' => $rmNumber,
                'avatar' => $avatarUrl,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'phone_number' => $request->phone_number,
                'street_address' => $request->street_address,
                'city_address' => $request->city_address,
                'state_address' => $request->state_address,
                'emergency_full_name' => $request->emergency_full_name,
                'emergency_phone_number' => $request->emergency_phone_number,
                'identity_number' => $request->identity_number,
                'bpjs_number' => $request->bpjs_number,
                'ethnic' => json_encode(['name' => $request->ethnic]), // Convert to JSON string like the API expects
                'education' => $request->education,
                'communication_barrier' => $request->communication_barrier,
                'disability_status' => $request->disability_status,
                'married_status' => $request->married_status,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'job' => $request->job,
                'blood_type' => $request->blood_type,
            ];

            // Send POST request to API
            $response = $client->request('POST', 'https://mockapi.pkuwsb.id/api/patient', [
                'headers' => [
                    'X-username' => 'admin',
                    'X-password' => 'secret',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $apiData,
                'timeout' => 30,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            // Log the API response
            Log::info('API Response:', $responseData);

            // Check if API response indicates success
            if ($responseData['success'] ?? false) {
                return redirect()->route('user.patient.records')->with('success', 'Data pasien berhasil ditambahkan dengan No. RM: ' . $rmNumber);
            } else {
                // Handle API validation errors
                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menyimpan data';
                $errors = $responseData['errors'] ?? [];

                if (!empty($errors)) {
                    return back()->withErrors($errors)->withInput();
                } else {
                    return back()->withErrors(['api' => $errorMessage])->withInput();
                }
            }

        } catch (RequestException $e) {

            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

                // Handle API validation errors (422 status)
                if ($e->getResponse()->getStatusCode() === 422) {
                    $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan validasi';
                    $errors = $responseData['errors'] ?? [];

                    if (!empty($errors)) {
                        return back()->withErrors($errors)->withInput();
                    } else {
                        return back()->withErrors(['api' => $errorMessage])->withInput();
                    }
                }

                // Handle other API errors
                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menghubungi server';
                return back()->withErrors(['api' => $errorMessage])->withInput();
            }

            return back()->withErrors(['api' => 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.'])->withInput();

        } catch (\Exception $e) {

            return back()->withErrors(['api' => 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Generate truly random 6-digit RM number
     */
    private function generateRandomRMNumber(): string
    {
        // Generate random 6-digit number between 100000-999999
        return str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Alternative: Generate random RM with retry logic for uniqueness
     * This method could be used if we need to check for uniqueness against database/API
     */
    private function generateUniqueRandomRMNumber(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            $rmNumber = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $attempt++;

            // In a real scenario, you'd check against database/API here
            // For now, just return the generated number
            return $rmNumber;

        } while ($attempt < $maxAttempts);

        // Fallback: use timestamp-based approach
        $timestamp = now()->timestamp;
        return substr((string)$timestamp, -6);
    }
}
