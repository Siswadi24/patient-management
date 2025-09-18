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
        // Validation rules - add rm_number as optional
        $rules = [
            'rm_number' => 'nullable|string|size:6', // Optional RM number input
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'street_address' => 'required|string|max:500',
            'city_address' => 'required|string|max:255',
            'state_address' => 'required|string|max:255',
            'emergency_full_name' => 'required|string|max:255',
            'emergency_phone_number' => 'required|string|max:20',
            'identity_number' => 'required|string|max:20',
            'bpjs_number' => 'nullable|string|max:20',
            'ethnic' => 'required|string|max:100',
            'education' => 'required|string|max:100',
            'communication_barrier' => 'nullable|string|max:255',
            'disability_status' => 'nullable|string|max:255',
            'married_status' => 'required|string|max:50',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'job' => 'required|string|max:100',
            'blood_type' => 'required|in:A,B,AB,O',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $messages = [
            'rm_number.size' => 'Nomor RM harus tepat 6 digit',
            'first_name.required' => 'Nama Depan harus diisi',
            'last_name.required' => 'Nama Belakang harus diisi',
            'gender.required' => 'Jenis Kelamin harus dipilih',
            'gender.in' => 'Jenis Kelamin tidak valid',
            'birth_place.required' => 'Tempat Lahir harus diisi',
            'birth_date.required' => 'Tanggal Lahir harus diisi',
            'birth_date.date' => 'Format Tanggal Lahir tidak valid',
            'phone_number.required' => 'Nomor Telepon harus diisi',
            'street_address.required' => 'Alamat Jalan harus diisi',
            'city_address.required' => 'Kota harus diisi',
            'state_address.required' => 'Provinsi harus diisi',
            'emergency_full_name.required' => 'Nama Kontak Darurat harus diisi',
            'emergency_phone_number.required' => 'Nomor Telepon Kontak Darurat harus diisi',
            'identity_number.required' => 'Nomor Identitas (KTP) harus diisi',
            'ethnic.required' => 'Suku/Etnis harus diisi',
            'education.required' => 'Pendidikan harus diisi',
            'married_status.required' => 'Status Pernikahan harus diisi',
            'father_name.required' => 'Nama Ayah harus diisi',
            'mother_name.required' => 'Nama Ibu harus diisi',
            'job.required' => 'Pekerjaan harus diisi',
            'blood_type.required' => 'Golongan Darah harus diisi',
            'blood_type.in' => 'Golongan Darah tidak valid',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'avatar.max' => 'Ukuran gambar maksimal 2MB',
        ];

        $attributes = [
            'rm_number' => 'Nomor RM',
            'first_name' => 'Nama Depan',
            'last_name' => 'Nama Belakang',
            'gender' => 'Jenis Kelamin',
            'birth_place' => 'Tempat Lahir',
            'birth_date' => 'Tanggal Lahir',
            'phone_number' => 'Nomor Telepon',
            'street_address' => 'Alamat Jalan',
            'city_address' => 'Kota',
            'state_address' => 'Provinsi',
            'emergency_full_name' => 'Nama Kontak Darurat',
            'emergency_phone_number' => 'Nomor Telepon Kontak Darurat',
            'identity_number' => 'Nomor Identitas (KTP)',
            'bpjs_number' => 'Nomor BPJS',
            'ethnic' => 'Suku/Etnis',
            'education' => 'Pendidikan',
            'communication_barrier' => 'Hambatan Komunikasi',
            'disability_status' => 'Status Disabilitas',
            'married_status' => 'Status Pernikahan',
            'father_name' => 'Nama Ayah',
            'mother_name' => 'Nama Ibu',
            'job' => 'Pekerjaan',
            'blood_type' => 'Golongan Darah',
            'avatar' => 'Foto Pasien',
        ];

        $validatedData = $request->validate($rules, $messages, $attributes);

        try {
            $client = new Client();

            // Generate or use provided RM number
            $rmNumber = $validatedData['rm_number'] ?? $this->generateRandomRMNumber();

            // Handle file upload for avatar
            $avatarUrl = 'https://api.dicebear.com/6.x/identicon/svg?seed=' . strtolower($validatedData['first_name'] . '.' . $validatedData['last_name']);

            // Prepare data for API - fix the ethnic field to be JSON string
            $apiData = [
                'rm_number' => $rmNumber,
                'avatar' => $avatarUrl,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'gender' => $validatedData['gender'],
                'birth_place' => $validatedData['birth_place'],
                'birth_date' => $validatedData['birth_date'],
                'phone_number' => $validatedData['phone_number'],
                'street_address' => $validatedData['street_address'],
                'city_address' => $validatedData['city_address'],
                'state_address' => $validatedData['state_address'],
                'emergency_full_name' => $validatedData['emergency_full_name'],
                'emergency_phone_number' => $validatedData['emergency_phone_number'],
                'identity_number' => $validatedData['identity_number'],
                'bpjs_number' => $validatedData['bpjs_number'],
                'ethnic' => json_encode($validatedData['ethnic']), // Convert to JSON string
                'education' => $validatedData['education'],
                'communication_barrier' => $validatedData['communication_barrier'],
                'disability_status' => $validatedData['disability_status'],
                'married_status' => $validatedData['married_status'],
                'father_name' => $validatedData['father_name'],
                'mother_name' => $validatedData['mother_name'],
                'job' => $validatedData['job'],
                'blood_type' => $validatedData['blood_type'],
            ];

            // Log the data being sent for debugging
            Log::info('Sending patient data to API:', $apiData);

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

            if ($responseData['success'] ?? false) {
                return redirect()->route('user.patient.records')->with('success', 'Data pasien berhasil ditambahkan dengan No. RM: ' . $rmNumber);
            } else {
                $errorMessage = $responseData['message'] ?? 'Terjadi kesalahan saat menyimpan data';
                return back()->withErrors(['api' => $errorMessage])->withInput();
            }

        } catch (RequestException $e) {
            Log::error('API Request failed:', [
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);

            $errorMessage = 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.';

            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseData = json_decode($responseBody, true);

                if (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                } elseif (isset($responseData['errors'])) {
                    // Handle validation errors from API
                    return back()->withErrors($responseData['errors'])->withInput();
                }
            }

            return back()->withErrors(['api' => $errorMessage])->withInput();

        } catch (\Exception $e) {
            Log::error('Unexpected error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['api' => 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.'])->withInput();
        }
    }

    /**
     * Generate truly random 6-digit RM number
     */
    private function generateRandomRMNumber(): string
    {
        // Generate random 6-digit number
        $rmNumber = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        return $rmNumber;
    }

    /**
     * Alternative: Generate random RM with retry logic for uniqueness
     */
    private function generateUniqueRandomRMNumber(): string
    {
        $maxAttempts = 10;
        $attempt = 0;

        do {
            // Generate random 6-digit number
            $rmNumber = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $attempt++;

            // In a real scenario, you'd check against database/API here
            // For now, just return the generated number
            return $rmNumber;

        } while ($attempt < $maxAttempts);

        // Fallback: use timestamp-based approach
        $timestamp = now()->timestamp;
        return substr((string)$timestamp, -6);


        // Ensure it's exactly 6 digits
        $rmNumber = str_pad($rmNumber, 6, '0', STR_PAD_LEFT);

        return $rmNumber;
    }
    /**
     * Alternative: Date-based with counter
     */
    private function generateDateBasedRM(): string
    {
        $currentDate = now();

        // Format: MMDDXX where XX is a random/sequential number
        $month = $currentDate->format('m');
        $day = $currentDate->format('d');

        // Generate 2-digit suffix (could be sequential from database in real app)
        $suffix = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);

        $rmNumber = $month . $day . $suffix;

        return $rmNumber;
    }
}
